@extends('layouts.retro')

@section('title', 'Mis Pedidos - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← MIS PEDIDOS →</h1>
        <div class="section-divider"></div>
    </div>

    @if($orders->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 4rem; color: #9E9890;"></i>
        <p class="mt-3 text-muted">No tienes pedidos realizados.</p>
        <a href="{{ route('catalog') }}" class="btn-retro-primary mt-2">
            <i class="bi bi-grid-3x3-gap-fill"></i> VER CATÁLOGO
        </a>
    </div>
    @else
    @foreach($orders as $order)
    <div class="order-card mb-3">
        <div class="order-header">
            <div>
                <span class="order-id">#{{ $order->order_number }}</span>
                <span class="order-date">{{ $order->created_at->format('d/m/Y') }}</span>
            </div>
            <span class="status-badge status-{{ $order->status }}">
                {{ strtoupper($order->status) }}
            </span>
        </div>
        <div class="order-items">
            @foreach($order->items as $item)
            <div>{{ $item->product_name }} x{{ $item->quantity }}</div>
            @endforeach
        </div>
        <div class="order-total">TOTAL: S/. {{ number_format($order->total , 2) }}</div>
        <div class="order-actions">
            <a href="{{ route('orders.show', $order) }}" class="btn-retro-primary btn-sm">
                <i class="bi bi-eye"></i> VER DETALLE
            </a>
        </div>
    </div>
    @endforeach
    @endif
</div>
@endsection
