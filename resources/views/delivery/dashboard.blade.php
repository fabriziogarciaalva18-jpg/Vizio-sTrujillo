@extends('layouts.retro')

@section('title', 'Panel de Repartidor - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← PANEL DE REPARTIDOR →</h1>
        <div class="section-divider"></div>
    </div>

    @if(session('success'))
        <div class="alert alert-retro" style="background: #DCFCE7; color: #166534;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B;">{{ session('error') }}</div>
    @endif

    <!-- Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $stats['available'] }}</h3>
                <small class="text-muted">Pedidos disponibles</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $stats['my_deliveries'] }}</h3>
                <small class="text-muted">Mis entregas</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $stats['delivered_today'] }}</h3>
                <small class="text-muted">Entregados hoy</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ now()->format('d/m/Y') }}</h3>
                <small class="text-muted">Fecha</small>
            </div>
        </div>
    </div>

    <!-- Pedidos disponibles -->
    @if($availableOrders->isNotEmpty())
        <div class="mb-4">
            <h3 class="mb-3"><i class="bi bi-box-seam"></i> Pedidos disponibles</h3>
            <div class="row g-3">
                @foreach($availableOrders as $order)
                    <div class="col-md-6 col-lg-4">
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">#{{ $order->order_number }}</span>
                                <span class="status-badge status-preparing">Preparado</span>
                            </div>
                            <div class="order-details">
                                <p><i class="bi bi-person"></i> {{ $order->user->name ?? 'N/A' }}</p>
                                <p><i class="bi bi-geo-alt"></i> {{ Str::limit($order->delivery_address, 40) }}</p>
                                <p><i class="bi bi-district"></i> {{ $order->district ?? 'Sin distrito' }}</p>
                            </div>
                            <div class="order-total">Total: S/. {{ number_format($order->total, 2) }}</div>
                            <div class="order-actions">
                                <form action="{{ route('delivery.take', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-retro-primary w-100">
                                        <i class="bi bi-check-circle"></i> TOMAR PEDIDO
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Mis entregas -->
    @if($myDeliveries->isNotEmpty())
        <div>
            <h3 class="mb-3"><i class="bi bi-truck"></i> Mis entregas</h3>
            <div class="row g-3">
                @foreach($myDeliveries as $order)
                    <div class="col-md-6 col-lg-4">
                        <div class="order-card" style="border-left: 4px solid #075985;">
                            <div class="order-header">
                                <span class="order-id">#{{ $order->order_number }}</span>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ $order->status == 'preparing' ? 'Preparado' : 'En camino' }}
                                </span>
                            </div>
                            <div class="order-details">
                                <p><i class="bi bi-person"></i> {{ $order->user->name ?? 'N/A' }}</p>
                                <p><i class="bi bi-geo-alt"></i> {{ Str::limit($order->delivery_address, 40) }}</p>
                                <p><i class="bi bi-district"></i> {{ $order->district ?? 'Sin distrito' }}</p>
                                @if($order->delivery_reference)
                                    <p><i class="bi bi-pin"></i> {{ Str::limit($order->delivery_reference, 30) }}</p>
                                @endif
                            </div>
                            <div class="order-total">Total: S/. {{ number_format($order->total, 2) }}</div>
                            <div class="order-actions">
                                <a href="{{ route('delivery.show', $order) }}" class="btn-retro-primary btn-sm w-100">
                                    <i class="bi bi-eye"></i> VER DETALLE
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($availableOrders->isEmpty() && $myDeliveries->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #9E9890;"></i>
            <p class="mt-3 text-muted">No hay pedidos disponibles ni asignados en este momento.</p>
        </div>
    @endif

    <!-- Historial -->
    @if($history->isNotEmpty())
        <div class="mt-4">
            <h3 class="mb-3"><i class="bi bi-clock-history"></i> Últimas entregas</h3>
            <div class="admin-card-retro">
                <table class="admin-table-retro">
                    <thead>
                        <tr><th>Pedido</th><th>Cliente</th><th>Total</th><th>Entregado</th></tr>
                    </thead>
                    <tbody>
                        @foreach($history as $order)
                            <tr>
                                <td>#{{ $order->order_number }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>S/. {{ number_format($order->total, 2) }}</td>
                                <td>{{ $order->delivered_at ? $order->delivered_at->diffForHumans() : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
