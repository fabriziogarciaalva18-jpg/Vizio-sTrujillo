@extends('layouts.retro')

@section('title', 'Mis Pedidos - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← MIS PEDIDOS →</h1>
        <div class="section-divider"></div>
    </div>

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
    @if($order->canBeCancelledByUser())
        <form action="{{ route('orders.cancel', $order) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn-retro-danger btn-sm" onclick="return confirm('¿Cancelar este pedido?')">
                <i class="bi bi-x-circle"></i> CANCELAR
            </button>
        </form>
    @endif
    <a href="{{ route('orders.show', $order) }}" class="btn-retro-secondary btn-sm">
        <i class="bi bi-eye"></i> VER DETALLE
    </a>
</div>
    </div>
    @endforeach
    @endif
</div>
@endsection
