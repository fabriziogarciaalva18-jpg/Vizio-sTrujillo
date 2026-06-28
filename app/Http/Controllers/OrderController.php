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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{
    public function checkout()
{
    $cart = session()->get('cart', []);
    if (empty($cart)) {
        return redirect()->route('catalog')->with('error', 'El carrito está vacío');
    }

    // Precio base sin envío
    $subtotal = 0;
    foreach ($cart as $item) {
        $price = $item['unit_price'] ?? $item['price'] ?? 0;
        $subtotal += $price * $item['quantity'];
    }

    $deliveryFee = 0;
    $deliveryDistance = 0;

    return view('orders.checkout', compact('cart', 'subtotal', 'deliveryFee', 'deliveryDistance'));
}

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
        // ✅ RE-VALIDAR EN EL SERVIDOR (evita que el usuario manipule el frontend)
        $locationService = new LocationService();
        $geo = $locationService->geocode($request->delivery_address);

        if (!$geo || !$geo['valid']) {
            return back()->with('error', $geo['error'] ?? 'Dirección inválida. Asegúrate de que esté en La Libertad.');
        }

        // Usar las coordenadas reales (no las que vengan del frontend)
        $lat = $geo['lat'];
        $lng = $geo['lng'];

        // Calcular distancia usando las coordenadas reales
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

        // Forzar los valores reales (ignorar lo que venga del frontend)
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

    // Guardar datos en sesión (con valores reales)
    session()->put('checkout_data', $request->all());

    return redirect()->route('payment.method', ['method' => $request->payment_method]);
}
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
     * Procesar pago y redirigir
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
}
