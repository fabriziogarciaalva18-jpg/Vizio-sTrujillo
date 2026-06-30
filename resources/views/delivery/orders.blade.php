@extends('layouts.retro')

@section('title', 'Entregas del día - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← ENTREGAS DEL DÍA →</h1>
        <div class="section-divider"></div>
    </div>

    <div class="admin-card-retro">
        <div class="table-responsive">
            <table class="admin-table-retro">
                <thead>
                    <tr>
                        <th><i class="bi bi-hash"></i> Pedido</th>
                        <th><i class="bi bi-person"></i> Cliente</th>
                        <th><i class="bi bi-geo-alt"></i> Dirección</th>
                        <th><i class="bi bi-phone"></i> Teléfono</th>
                        <th><i class="bi bi-currency-dollar"></i> Total</th>
                        <th><i class="bi bi-gear"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                        <td>
                            <div class="small">
                                <strong>{{ $order->district }}</strong><br>
                                <span class="text-muted">{{ $order->delivery_address }}</span>
                                @if($order->reference_point)
                                    <br><i class="bi bi-pin"></i> {{ $order->reference_point }}
                                @endif
                                @if($order->latitude && $order->longitude)
                                    <br>
                                    <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="btn-retro-secondary btn-sm mt-1">
                                        <i class="bi bi-map"></i> Ver en mapa
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td>{{ $order->phone }}</td>
                        <td>S/. {{ number_format($order->total, 2) }}</td>
                        <td>
                            <a href="{{ route('delivery.show', $order) }}" class="btn-retro-primary btn-sm">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2">No hay pedidos preparados para entregar hoy.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
</div>
@endsection
