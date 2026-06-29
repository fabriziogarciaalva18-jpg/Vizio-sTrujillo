<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DeliveryController extends Controller implements HasMiddleware
{
    /**
     * Define los middlewares que se aplicarán a este controlador.
     */
    public static function middleware(): array
{
    return [
        new Middleware('auth'),
        new Middleware('verified'),
        new Middleware(\App\Http\Middleware\CheckIsDelivery::class), // ✅ Usar clase directamente
    ];
}

    /**
     * Dashboard del repartidor: pedidos disponibles, asignados e historial.
     */
    public function dashboard()
    {
        $today = Carbon::today();

        // Pedidos disponibles para tomar (preparing, sin repartidor, fecha de hoy)
        $availableOrders = Order::where('status', 'preparing')
            ->whereNull('delivery_person_id')
            ->whereDate('delivery_date', $today)
            ->with('user')
            ->orderBy('delivery_date', 'asc')
            ->get();

        // Pedidos asignados a este repartidor
        $myDeliveries = Order::where('delivery_person_id', auth()->id())
            ->whereIn('status', ['preparing', 'delivering'])
            ->whereDate('delivery_date', $today)
            ->with('user')
            ->orderBy('delivery_date', 'asc')
            ->get();

        // Historial de entregas (últimos 5)
        $history = Order::where('delivery_person_id', auth()->id())
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->limit(5)
            ->get();

        // Estadísticas
        $stats = [
            'available' => $availableOrders->count(),
            'my_deliveries' => $myDeliveries->count(),
            'delivered_today' => Order::where('delivery_person_id', auth()->id())
                ->where('status', 'delivered')
                ->whereDate('delivered_at', $today)
                ->count(),
        ];

        return view('delivery.dashboard', compact('availableOrders', 'myDeliveries', 'history', 'stats'));
    }

    /**
     * Lista de entregas del día (con paginación)
     */
    public function orders()
    {
        $today = Carbon::today();

        $orders = Order::where('status', 'preparing')
            ->whereDate('delivery_date', $today)
            ->with('user')
            ->orderBy('delivery_date', 'asc')
            ->paginate(15);

        $storeLocation = [
            'lat' => config('shipping.store.lat', -8.1191),
            'lng' => config('shipping.store.lng', -79.0330),
        ];

        return view('delivery.orders', compact('orders', 'storeLocation'));
    }

    /**
     * Tomar un pedido (asignárselo)
     */
    public function takeOrder(Order $order)
    {
        if (!$order->isAvailableForDelivery()) {
            return redirect()->route('delivery.dashboard')
                ->with('error', 'Este pedido ya no está disponible.');
        }

        $order->assignToDelivery(auth()->id());

        return redirect()->route('delivery.dashboard')
            ->with('success', 'Pedido #' . $order->order_number . ' asignado correctamente.');
    }

    /**
     * Ver detalle de una entrega asignada
     */
    public function show(Order $order)
    {
        if ($order->delivery_person_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        $storeLocation = [
            'lat' => config('shipping.store.lat', -8.1191),
            'lng' => config('shipping.store.lng', -79.0330),
        ];

        return view('delivery.show', compact('order', 'storeLocation'));
    }

    /**
     * Confirmar entrega
     */
    public function confirmDelivery(Request $request, Order $order)
    {
        if ($order->delivery_person_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'delivery_note' => 'nullable|string|max:255',
        ]);

        if ($order->status !== 'delivering' && $order->status !== 'preparing') {
            return redirect()->route('delivery.dashboard')
                ->with('error', 'Este pedido no está en estado de entrega.');
        }

        DB::beginTransaction();

        try {
            $order->status = 'delivered';
            $order->delivered_at = now();
            $order->save();

            Delivery::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'delivery_person_id' => auth()->id(),
                    'delivered_at' => now(),
                    'delivery_notes' => $request->delivery_note,
                    'status' => 'delivered',
                ]
            );

            DB::commit();

            return redirect()->route('delivery.dashboard')
                ->with('success', 'Pedido entregado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al confirmar: ' . $e->getMessage());
        }
    }

    /**
     * Marcar como fallido
     */
    public function markAsFailed(Request $request, Order $order)
    {
        if ($order->delivery_person_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'failure_reason' => 'required|string|max:255',
        ]);

        if ($order->status !== 'delivering' && $order->status !== 'preparing') {
            return redirect()->route('delivery.dashboard')
                ->with('error', 'Este pedido no se puede marcar como fallido.');
        }

        $order->status = 'pending';
        $order->delivery_person_id = null;
        $order->save();

        Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'delivery_person_id' => auth()->id(),
                'status' => 'failed',
                'delivery_notes' => $request->failure_reason,
            ]
        );

        return redirect()->route('delivery.dashboard')
            ->with('error', 'Entrega marcada como fallida. El pedido volverá a la cola.');
    }

    /**
     * Actualizar ubicación del repartidor (vía AJAX)
     */
    public function updateLocation(Request $request, Order $order)
    {
        if ($order->delivery_person_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'lat' => 'required|numeric|between:-20,-5',
            'lng' => 'required|numeric|between:-85,-70',
        ]);

        $order->delivery_person_lat = $request->lat;
        $order->delivery_person_lng = $request->lng;
        $order->last_location_update = now();
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Ubicación actualizada',
            'location' => [
                'lat' => $request->lat,
                'lng' => $request->lng,
                'updated_at' => now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Obtener la ubicación actual del repartidor (para el cliente)
     */
    public function getLocation(Order $order)
    {
        if ($order->delivery_person_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'location' => [
                'lat' => $order->delivery_person_lat,
                'lng' => $order->delivery_person_lng,
                'updated_at' => $order->last_location_update ? $order->last_location_update->toIso8601String() : null,
            ]
        ]);
    }

    // =============================================
    // MÉTODOS AUXILIARES
    // =============================================

    /**
     * Calcular distancia total de todas las entregas
     */
    private function calculateTotalDistance($orders)
    {
        $storeLat = config('shipping.store.lat', -8.1191);
        $storeLng = config('shipping.store.lng', -79.0330);
        $total = 0;

        foreach ($orders as $order) {
            if ($order->latitude && $order->longitude) {
                $total += $this->haversineDistance(
                    $storeLat,
                    $storeLng,
                    $order->latitude,
                    $order->longitude
                );
            }
        }

        return round($total, 2);
    }

    /**
     * Distancia en línea recta (Haversine)
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
