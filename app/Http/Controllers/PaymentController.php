<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Procesar pago según método (redirige a vista de instrucciones)
     */
    public function process(Request $request, $method)
    {
        $cart = session()->get('cart', []);
        $checkoutData = session()->get('checkout_data', []);

        if (empty($cart) || empty($checkoutData)) {
            return redirect()->route('cart')->with('error', 'No hay datos del pedido');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'];
        }

        // Obtener el delivery_fee desde checkoutData (calculado en el checkout)
        $deliveryFee = $checkoutData['delivery_fee'] ?? config('payments.delivery_fee', 800);
        $total = $subtotal + $deliveryFee;

        // Para contraentrega, crear pedido inmediatamente
        if ($method == 'contraentrega') {
            $order = $this->createOrder($cart, $checkoutData, 'pending_delivery');

            session()->forget('cart');
            session()->forget('checkout_data');

            return redirect()->route('orders.show', $order)
                ->with('success', '¡Pedido confirmado! Pagarás al momento de la entrega.');
        }

        // Para otros métodos (Yape, Plin, Transferencia), mostrar instrucciones
        return view('payments.show', compact('method', 'cart', 'checkoutData', 'total', 'subtotal', 'deliveryFee'));
    }

    /**
     * Crear pedido definitivamente (solo después de confirmar pago)
     */
    public function createOrder($cart, $checkoutData, $paymentStatus = 'pending')
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'];
        }

        // Obtener delivery_fee desde checkoutData
        $deliveryFee = $checkoutData['delivery_fee'] ?? config('payments.delivery_fee', 800);
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
     * Mostrar página de pago
     */
/**
 * Mostrar página de pago (o procesar contraentrega directamente)
 */
public function showPayment($method)
{
    $cart = session()->get('cart', []);
    $checkoutData = session()->get('checkout_data', []);

    if (empty($cart) || empty($checkoutData)) {
        return redirect()->route('cart')->with('error', 'No hay datos del pedido');
    }

    // ✅ Contraentrega: crear pedido inmediatamente y redirigir
    if ($method == 'contraentrega') {
        $order = $this->createOrder($cart, $checkoutData, 'pending_delivery');
        session()->forget('cart');
        session()->forget('checkout_data');
        return redirect()->route('orders.show', $order)
            ->with('success', '¡Pedido confirmado! Pagarás al momento de la entrega.');
    }

    // ✅ Otros métodos: mostrar vista de pago con instrucciones
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += ($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'];
    }

    $deliveryFee = $checkoutData['delivery_fee'] ?? 0;
    $total = $subtotal + $deliveryFee;

    return view('payments.show', compact('method', 'cart', 'checkoutData', 'total', 'subtotal', 'deliveryFee'));
}
    /**
     * Subir comprobante (Yape, Plin, Transferencia)
     */
    public function uploadVoucher(Request $request)
    {
        $request->validate([
            'payment_reference' => 'required|string|max:100',
            'voucher' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $cart = session()->get('cart', []);
        $checkoutData = session()->get('checkout_data', []);

        if (empty($cart) || empty($checkoutData)) {
            return redirect()->route('cart')->with('error', 'No hay datos del pedido');
        }

        // Guardar comprobante
        $file = $request->file('voucher');
        $filename = 'voucher_' . time() . '_' . auth()->id() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('vouchers', $filename, 'public');

        // Crear pedido con estado "pending_review"
        $order = $this->createOrder($cart, $checkoutData, 'pending_review');

        $order->update([
            'voucher_path' => $path,
            'payment_reference' => $request->payment_reference,
        ]);

        // Limpiar carrito y datos de checkout
        session()->forget('cart');
        session()->forget('checkout_data');

        return redirect()->route('orders.show', $order)
            ->with('success', 'Comprobante enviado. Revisaremos tu pago en las próximas horas.');
    }

    /**
     * Mostrar confirmación
     */
    public function confirmPayment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payments.confirmation', compact('order'));
    }

    /**
     * Marcar como pagado (Admin)
     */
    public function markAsPaid(Order $order)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'paid_at' => now(),
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pago confirmado. El pedido está en preparación.');
    }

    /**
     * Confirmar pedido contraentrega
     */
    public function confirmOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_method !== 'contraentrega') {
            return back()->with('error', 'Método de pago no válido');
        }

        $order->update([
            'payment_status' => 'pending_delivery',
            'status' => 'confirmed',
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pedido confirmado. Pagarás al momento de la entrega.');
    }

    /**
     * Cancelar pedido y restaurar carrito
     */
    public function cancelOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Restaurar carrito desde los items del pedido
        $cart = session()->get('cart', []);
        foreach ($order->items as $item) {
            if (isset($cart[$item->product_id])) {
                $cart[$item->product_id]['quantity'] += $item->quantity;
            } else {
                $cart[$item->product_id] = [
                    'id' => $item->product_id,
                    'name' => $item->product_name,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                ];
            }
        }
        session()->put('cart', $cart);

        $order->delete();

        return redirect()->route('cart')
            ->with('success', 'Pedido cancelado. Los productos han vuelto a tu carrito.');
    }
}
