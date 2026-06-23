<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
{
    $query = Order::with('user');

    // Filtros
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('order_number', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function($u) use ($search) {
                  $u->where('name', 'LIKE', "%{$search}%");
              });
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $orders = $query->latest()->paginate(20);

    // Estadísticas globales
    $statusCounts = [
        'pending'    => Order::where('status', 'pending')->count(),
        'confirmed'  => Order::where('status', 'confirmed')->count(),
        'preparing'  => Order::where('status', 'preparing')->count(),
        'delivering' => Order::where('status', 'delivering')->count(),
        'delivered'  => Order::where('status', 'delivered')->count(),
        'cancelled'  => Order::where('status', 'cancelled')->count(),
        'rejected'   => Order::where('status', 'rejected')->count(),
    ];

    return view('admin.orders', compact('orders', 'statusCounts'));
}

    public function show(Order $order)
    {
        return view('admin.orders-show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // 🔒 No permitir cambiar si el pedido ya está cancelado por el usuario
        if ($order->status === 'cancelled') {
            return redirect()->back()->with('error', 'No se puede modificar un pedido cancelado por el usuario.');
        }

        // 🔒 No permitir cambiar si el pedido ya fue rechazado (y no es para cambiarlo a otro estado)
        if ($order->status === 'rejected' && $request->status !== 'rejected') {
            return redirect()->back()->with('error', 'Un pedido rechazado no puede cambiar a otro estado.');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,delivering,delivered,cancelled,rejected'
        ]);

        // Si el estado es 'rejected', también marcamos el pago como rechazado
        if ($request->status === 'rejected') {
            $order->payment_status = 'rejected';
            // Opcional: también puedes poner el estado de pedido como 'cancelled' o mantener 'rejected'
        }

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Estado del pedido actualizado');
    }
}
