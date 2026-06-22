<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Mostrar el carrito de compras
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('orders.cart', compact('cart', 'total'));
    }

    /**
     * Agregar producto al carrito
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:99'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->base_price,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => $this->getCartCount()]);
        }

        return redirect()->back()->with('success', $product->name . ' agregado al carrito');
    }

    /**
     * Eliminar producto del carrito
     */
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Producto eliminado');
    }

    /**
     * Actualizar cantidad
     */
    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart');
    }

    /**
     * Mostrar checkout (formulario de datos de envío)
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('catalog')->with('error', 'El carrito está vacío');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $delivery_fee = config('payments.delivery_fee', 800);
        $total = $subtotal + $delivery_fee;

        return view('orders.checkout', compact('cart', 'subtotal', 'delivery_fee', 'total'));
    }

    /**
     * Procesar pago y crear pedido SOLO después de confirmar
     */
    /**
 * Procesar pago y crear pedido SOLO después de confirmar
 */
/**
 * Procesar pago y crear pedido SOLO después de confirmar
 */
public function processPayment(Request $request)
{
    $request->validate([
        'delivery_address' => 'required|string',
        'district' => 'required|string',
        'phone' => 'required|string',
        'delivery_date' => 'required|date|after:today',
        'payment_method' => 'required|in:yape,plin,transferencia,contraentrega',
        'special_instructions' => 'nullable|string',
    ]);

    $cart = session()->get('cart', []);

    if (empty($cart)) {
        return redirect()->route('cart')->with('error', 'El carrito está vacío');
    }

    // Guardar datos del checkout en sesión
    session()->put('checkout_data', $request->all());

    // Redirigir a la página de pago según el método (NUEVA RUTA)
    return redirect()->route('payment.method', ['method' => $request->payment_method]);
}

    /**
     * Crear pedido definitivamente (solo después de confirmar pago)
     */
    public function createOrder($cart, $checkoutData, $paymentStatus = 'pending')
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $deliveryFee = config('payments.delivery_fee', 800);
        $total = $subtotal + $deliveryFee;

        $orderNumber = 'VIZ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        DB::beginTransaction();

        try {
            // Crear el pedido
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'order_type' => 'delivery',
                'status' => $paymentStatus == 'paid' ? 'confirmed' : 'pending',
                'delivery_date' => $checkoutData['delivery_date'],
                'delivery_address' => $checkoutData['delivery_address'],
                'district' => $checkoutData['district'],
                'phone' => $checkoutData['phone'],
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $checkoutData['payment_method'],
                'payment_status' => $paymentStatus,
                'special_instructions' => $checkoutData['special_instructions'] ?? null,
                'paid_at' => $paymentStatus == 'paid' ? now() : null,
            ]);

            // Crear los items del pedido
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'configuration' => json_encode($item['customizations'] ?? []),
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
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

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
        if ($order->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'No se puede cancelar este pedido');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Pedido cancelado correctamente');
    }
}
