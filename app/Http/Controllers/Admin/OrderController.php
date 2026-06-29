<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductConfiguration;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Lista de pedidos con filtros (admin)
     */
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

    /**
     * Ver detalle de un pedido (admin)
     */
    public function show(Order $order)
    {
        return view('admin.orders-show', compact('order'));
    }

    /**
     * Actualizar el estado de un pedido (admin)
     */
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
            $order->payment_status = 'failed';
        }

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Estado del pedido actualizado');
    }

    /**
     * Actualizar un item del carrito desde el modal de edición (usado por el frontend)
     * Nota: Este método debería estar en OrderController, pero se incluye aquí para mantener la estructura del usuario.
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

        // Generar nueva clave para el carrito
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
