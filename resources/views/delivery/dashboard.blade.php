@extends('layouts.retro')

@section('title', 'Mis Entregas - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← MIS ENTREGAS →</h1>
        <div class="section-divider"></div>
    </div>

    @if(session('success'))
        <div class="alert alert-retro" style="background: #DCFCE7; color: #166534;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B;">{{ session('error') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #9E9890;"></i>
            <p class="mt-3 text-muted">No tienes pedidos asignados en este momento.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-md-6 col-lg-4">
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">#{{ $order->order_number }}</span>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="order-details">
                            <p><i class="bi bi-person"></i> {{ $order->user->name ?? 'N/A' }}</p>
                            <p><i class="bi bi-geo-alt"></i> {{ $order->delivery_address }}</p>
                            <p><i class="bi bi-district"></i> {{ $order->district ?? 'Sin distrito' }}</p>
                            @if($order->delivery_reference)
                                <p><i class="bi bi-pin"></i> {{ $order->delivery_reference }}</p>
                            @endif
                        </div>
                        <div class="order-total">Total: S/. {{ number_format($order->total, 2) }}</div>
                        <div class="order-actions">
                            <a href="{{ route('delivery.orders.show', $order) }}" class="btn-retro-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> VER DETALLE
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
