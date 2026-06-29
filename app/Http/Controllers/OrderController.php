<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use App\Services\DeliveryFeeService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductConfiguration;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Mostrar el carrito de compras (normaliza items)
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        // Normalizar items (asegurar unit_price)
        $normalizedCart = [];
        foreach ($cart as $key => $item) {
            if (!isset($item['unit_price'])) {
                $item['unit_price'] = $item['price'] ?? $item['base_price'] ?? 0;
                $cart[$key]['unit_price'] = $item['unit_price'];
            }
            if (!isset($item['quantity'])) {
                $item['quantity'] = 1;
                $cart[$key]['quantity'] = 1;
            }
            $normalizedCart[$key] = $item;
            $total += $item['unit_price'] * $item['quantity'];
        }
        session()->put('cart', $cart);

        return view('orders.cart', compact('cart', 'total'));
    }

    /**
     * Agregar producto al carrito
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1|max:99'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);

        $selectedConfigs = $request->input('configurations', []);
        $selectedAddons   = $request->input('addons', []);
        $message = $request->input('message', '');

        // Calcular precio unitario con extras
        $unitPrice = $product->base_price;

        foreach ($selectedConfigs as $type => $configId) {
            $config = ProductConfiguration::find($configId);
            if ($config) {
                $unitPrice += $config->price_modifier;
            }
        }

        foreach ($selectedAddons as $addonId) {
            $addon = Addon::find($addonId);
            if ($addon) {
                $unitPrice += $addon->price;
            }
        }

        $messagePrice = $request->input('message_price', 0);
        if (!empty($message) && $messagePrice > 0) {
            $unitPrice += $messagePrice;
        }

        // Guardar en sesión
        $cart = session()->get('cart', []);
        $itemKey = $product->id . '_' . md5(json_encode($selectedConfigs) . json_encode($selectedAddons) . $message);

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
        } else {
            $cart[$itemKey] = [
                'id'               => $product->id,
                'name'             => $product->name,
                'base_price'       => $product->base_price,
                'unit_price'       => $unitPrice,
                'quantity'         => $quantity,
                'selected_configs' => $selectedConfigs,
                'selected_addons'  => $selectedAddons,
                'message'          => $message,
                'image'            => $product->image_url,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', $product->name . ' agregado al carrito');
    }

    /**
     * Eliminar producto del carrito (JSON)
     */
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return response()->json(['success' => true, 'message' => 'Producto eliminado']);
        }
        return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
    }

    /**
     * Actualizar cantidad
     */
    public function updateCart(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    /**
     * Mostrar checkout (formulario de datos de envío y ubicación)
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('catalog')->with('error', 'El carrito está vacío');
        }

        // Calcular subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $price = $item['unit_price'] ?? $item['price'] ?? 0;
            $subtotal += $price * $item['quantity'];
        }

        $deliveryFee = 0;
        $deliveryDistance = 0;

        return view('orders.checkout', compact('cart', 'subtotal', 'deliveryFee', 'deliveryDistance'));
    }

    /**
     * Procesar pago y redirigir a la página de pago
     */
    public function processPayment(Request $request)
    {
        // Validaciones básicas
        $request->validate([
            'delivery_address' => 'required_if:delivery_type,delivery|string|max:255',
            'district' => 'nullable|string|max:255',
            'phone' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!preg_match('/^9\d{8}$/', $value)) {
                    $fail('El teléfono debe tener 9 dígitos y comenzar con 9 (ej: 987654321).');
                }
            }],
            'delivery_date' => 'required|date|after:today',
            'payment_method' => 'required|in:yape,plin,transferencia,contraentrega',
            'special_instructions' => 'nullable|string',
            'delivery_type' => 'required|in:pickup,delivery',
            'address_lat' => 'required_if:delivery_type,delivery|numeric|between:-20,-5',
            'address_lng' => 'required_if:delivery_type,delivery|numeric|between:-85,-70',
            'delivery_distance' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_reference' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'El carrito está vacío');
        }

        $deliveryType = $request->delivery_type;
        $deliveryFee = 0;
        $deliveryDistance = 0;
        $lat = null;
        $lng = null;

        if ($deliveryType === 'delivery') {
    $locationService = new LocationService();
    $geo = $locationService->geocode($request->delivery_address);

    // ✅ Ahora $geo siempre tiene 'valid' => true (por el fallback)
    if (!$geo) {
        return back()->with('error', 'No se pudo verificar la dirección.')->withInput();
    }

    // Si hay advertencia, mostrarla
    if (!empty($geo['warning'])) {
        session()->flash('warning', $geo['warning']);
    }

    $lat = $geo['lat'];
    $lng = $geo['lng'];

    // Calcular distancia y tarifa
    $storeLat = config('delivery.store.lat');
    $storeLng = config('delivery.store.lng');
    $distance = $locationService->calculateDistance($storeLat, $storeLng, $lat, $lng);
    $deliveryDistance = $distance;

    $feeService = new DeliveryFeeService();
    $feeResult = $feeService->calculate($distance);

    if (!$feeResult['valid']) {
        return back()->with('error', $feeResult['error']);
    }

    $deliveryFee = $feeResult['fee'];

    $request->merge([
        'address_lat' => $lat,
        'address_lng' => $lng,
        'delivery_distance' => $deliveryDistance,
        'delivery_fee' => $deliveryFee,
    ]);
} else {
            // Recojo en tienda: dirección fija y sin costo de envío
            $request->merge([
                'delivery_address' => 'Recojo en tienda - Los Cedros 154, Víctor Larco Herrera',
                'address_lat' => null,
                'address_lng' => null,
                'delivery_distance' => null,
                'delivery_fee' => 0,
            ]);
        }
        if ($request->payment_method === 'contraentrega' && $request->delivery_type === 'pickup') {
    return back()->with('error', 'El método de pago "Contra entrega" no está disponible para recojo en tienda.')->withInput();
}

        // Guardar datos en sesión (con valores reales)
        session()->put('checkout_data', [
            'delivery_address' => $request->delivery_address,
            'district' => $request->district,
            'phone' => $request->phone,
            'delivery_date' => $request->delivery_date,
            'payment_method' => $request->payment_method,
            'special_instructions' => $request->special_instructions,
            'delivery_type' => $request->delivery_type,
            'address_lat' => $request->address_lat,
            'address_lng' => $request->address_lng,
            'delivery_distance' => $request->delivery_distance,
            'delivery_fee' => $request->delivery_fee,
            'delivery_reference' => $request->delivery_reference,
        ]);

        return redirect()->route('payment.method', ['method' => $request->payment_method]);
    }

    /**
     * Crear pedido definitivamente (llamado desde PaymentController)
     */
    public function createOrder($cart, $checkoutData, $paymentStatus = 'pending')
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'];
        }

        $deliveryFee = $checkoutData['delivery_fee'] ?? 0;
        $total = $subtotal + $deliveryFee;

        $orderNumber = 'VIZ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'order_type' => 'delivery',
                'delivery_type' => $checkoutData['delivery_type'] ?? 'delivery',
                'status' => $paymentStatus == 'paid' ? 'confirmed' : ($paymentStatus == 'pending_delivery' ? 'confirmed' : 'pending'),
                'delivery_date' => $checkoutData['delivery_date'],
                'delivery_address' => $checkoutData['delivery_address'],
                'district' => $checkoutData['district'] ?? null,
                'phone' => $checkoutData['phone'],
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'delivery_distance' => $checkoutData['delivery_distance'] ?? null,
                'address_lat' => $checkoutData['address_lat'] ?? null,
                'address_lng' => $checkoutData['address_lng'] ?? null,
                'total' => $total,
                'payment_method' => $checkoutData['payment_method'],
                'payment_status' => $paymentStatus,
                'special_instructions' => $checkoutData['special_instructions'] ?? null,
                'delivery_reference' => $checkoutData['delivery_reference'] ?? null,
                'paid_at' => $paymentStatus == 'paid' ? now() : null,
            ]);

            foreach ($cart as $item) {
                $customization = [
                    'selected_configs' => $item['selected_configs'] ?? [],
                    'selected_addons'  => $item['selected_addons'] ?? [],
                    'message'          => $item['message'] ?? '',
                ];
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['id'],
                    'product_name'  => $item['name'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'] ?? $item['price'] ?? 0,
                    'subtotal'      => ($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'],
                    'configuration' => json_encode($customization),
                ]);
            }

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Actualizar un item del carrito desde el modal de edición
     */
    public function updateCartItem(Request $request, $key)
    {
        $cart = session()->get('cart', []);
        if (!isset($cart[$key])) {
            return response()->json(['success' => false, 'message' => 'Item no encontrado'], 404);
        }

        $oldItem = $cart[$key];

        // Recoger datos del formulario
        $selectedConfigs = $request->input('configurations', []);
        $selectedAddons = $request->input('addons', []);
        $message = $request->input('message', '');
        $quantity = $request->input('quantity', 1);

        // Calcular nuevo precio unitario
        $product = Product::find($oldItem['id']);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        $unitPrice = $product->base_price;

        foreach ($selectedConfigs as $type => $configId) {
            $config = ProductConfiguration::find($configId);
            if ($config) {
                $unitPrice += $config->price_modifier;
            }
        }

        foreach ($selectedAddons as $addonId) {
            $addon = Addon::find($addonId);
            if ($addon) {
                $unitPrice += $addon->price;
            }
        }

        $messagePrice = $request->input('message_price', 0);
        if (!empty($message) && $messagePrice > 0) {
            $unitPrice += $messagePrice;
        }

        // Generar nueva clave
        $newKey = $product->id . '_' . md5(json_encode($selectedConfigs) . json_encode($selectedAddons) . $message);

        // Si la clave cambia, eliminar el viejo y añadir el nuevo
        if ($newKey !== $key) {
            unset($cart[$key]);
            if (isset($cart[$newKey])) {
                $cart[$newKey]['quantity'] += $quantity;
            } else {
                $cart[$newKey] = [
                    'id'               => $product->id,
                    'name'             => $product->name,
                    'base_price'       => $product->base_price,
                    'unit_price'       => $unitPrice,
                    'quantity'         => $quantity,
                    'selected_configs' => $selectedConfigs,
                    'selected_addons'  => $selectedAddons,
                    'message'          => $message,
                    'image'            => $product->image_url,
                ];
            }
        } else {
            // Misma clave, actualizar cantidad y precios
            $cart[$key]['quantity'] = $quantity;
            $cart[$key]['unit_price'] = $unitPrice;
            $cart[$key]['selected_configs'] = $selectedConfigs;
            $cart[$key]['selected_addons'] = $selectedAddons;
            $cart[$key]['message'] = $message;
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true, 'message' => 'Carrito actualizado']);
    }

    /**
     * Listar pedidos del usuario autenticado
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Mostrar detalle de un pedido específico
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Cancelar un pedido (solo si está pendiente)
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'pending_review'])) {
            return back()->with('error', 'No se puede cancelar este pedido porque ya está en proceso o confirmado.');
        }

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'No se puede cancelar un pedido ya pagado.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Pedido cancelado correctamente.');
    }
}
