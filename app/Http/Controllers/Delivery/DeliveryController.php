<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    /**
     * Dashboard del repartidor
     */
    public function dashboard()
    {
        $today = Carbon::today();

        // Pedidos preparados del día de hoy (para entregar)
        $deliveries = Order::where('status', 'preparing')
            ->whereDate('delivery_date', $today)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas
        $stats = [
            'total_today' => $deliveries->count(),
            'delivered_today' => Order::where('status', 'delivered')
                ->whereDate('delivery_date', $today)
                ->count(),
            'pending_today' => Order::where('status', 'preparing')
                ->whereDate('delivery_date', $today)
                ->count(),
            'total_km' => $this->calculateTotalDistance($deliveries),
        ];

        return view('delivery.dashboard', compact('deliveries', 'stats'));
    }

    /**
     * Lista de entregas del día
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
     * Ver detalle de una entrega
     */
    public function show(Order $order)
    {
        // Verificar que sea del día actual y esté preparado
        if ($order->status !== 'preparing' || $order->delivery_date->format('Y-m-d') !== Carbon::today()->format('Y-m-d')) {
            return redirect()->route('delivery.dashboard')->with('error', 'Este pedido no está disponible para entrega.');
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
        $request->validate([
            'delivery_note' => 'nullable|string|max:255',
        ]);

        // Verificar que sea del día actual y esté preparado
        if ($order->status !== 'preparing' || $order->delivery_date->format('Y-m-d') !== Carbon::today()->format('Y-m-d')) {
            return redirect()->route('delivery.dashboard')->with('error', 'Este pedido no está disponible para entrega.');
        }

        DB::beginTransaction();

        try {
            // Actualizar estado del pedido
            $order->status = 'delivered';
            $order->delivered_at = now();
            $order->save();

            // Crear o actualizar registro de entrega
            $delivery = Delivery::updateOrCreate(
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
            return redirect()->back()->with('error', 'Error al confirmar la entrega: ' . $e->getMessage());
        }
    }

    /**
     * Marcar como no entregado (problema)
     */
    public function markAsFailed(Request $request, Order $order)
    {
        $request->validate([
            'failure_reason' => 'required|string|max:255',
        ]);

        if ($order->status !== 'preparing' || $order->delivery_date->format('Y-m-d') !== Carbon::today()->format('Y-m-d')) {
            return redirect()->route('delivery.dashboard')->with('error', 'Este pedido no está disponible.');
        }

        $delivery = Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'delivery_person_id' => auth()->id(),
                'status' => 'failed',
                'delivery_notes' => $request->failure_reason,
            ]
        );

        // Volver a pending para que el admin lo reassigne
        $order->status = 'pending';
        $order->save();

        return redirect()->route('delivery.dashboard')
            ->with('error', 'Entrega marcada como fallida. El pedido volverá a la cola.');
    }

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
