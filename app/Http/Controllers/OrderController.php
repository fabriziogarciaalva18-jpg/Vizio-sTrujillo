<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductConfiguration;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Mostrar checkout
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('catalog')->with('error', 'El carrito está vacío');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $price = $item['unit_price'] ?? $item['price'] ?? 0;
            $subtotal += $price * $item['quantity'];
        }
        $delivery_fee = config('payments.delivery_fee', 800);
        $total = $subtotal + $delivery_fee;

        return view('orders.checkout', compact('cart', 'subtotal', 'delivery_fee', 'total'));
    }

    /**
     * Procesar pago y redirigir
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string',
            'district'         => 'required|string',
            'phone'            => 'required|string',
            'delivery_date'    => 'required|date|after:today',
            'payment_method'   => 'required|in:yape,plin,transferencia,contraentrega',
            'special_instructions' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'El carrito está vacío');
        }

        session()->put('checkout_data', $request->all());
        return redirect()->route('payment.method', ['method' => $request->payment_method]);
    }

    /**
     * Crear pedido definitivo
     */
    public function createOrder($cart, $checkoutData, $paymentStatus = 'pending')
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $price = $item['unit_price'] ?? $item['price'] ?? 0;
            $subtotal += $price * $item['quantity'];
        }

        $deliveryFee = config('payments.delivery_fee', 800);
        $total = $subtotal + $deliveryFee;
        $orderNumber = 'VIZ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id'            => auth()->id(),
                'order_number'       => $orderNumber,
                'order_type'         => 'delivery',
                'status'             => $paymentStatus == 'paid' ? 'confirmed' : 'pending',
                'delivery_date'      => $checkoutData['delivery_date'],
                'delivery_address'   => $checkoutData['delivery_address'],
                'district'           => $checkoutData['district'],
                'phone'              => $checkoutData['phone'],
                'subtotal'           => $subtotal,
                'delivery_fee'       => $deliveryFee,
                'total'              => $total,
                'payment_method'     => $checkoutData['payment_method'],
                'payment_status'     => $paymentStatus,
                'special_instructions' => $checkoutData['special_instructions'] ?? null,
                'paid_at'            => $paymentStatus == 'paid' ? now() : null,
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

    private function getCartCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        if (!in_array($order->status, ['pending', 'pending_review'])) {
            return back()->with('error', 'No se puede cancelar este pedido.');
        }
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'No se puede cancelar un pedido ya pagado.');
        }
        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Pedido cancelado.');
    }
}
