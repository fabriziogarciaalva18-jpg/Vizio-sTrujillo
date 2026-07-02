@extends('layouts.retro')

@section('title', 'Mis Pedidos - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title" style="font-size: 1.8rem;">← MIS PEDIDOS →</h1>
            <div class="section-divider"></div>
        </div>
        <a href="{{ route('catalog') }}" class="btn-retro-primary">
            <i class="bi bi-plus-circle"></i> NUEVO PEDIDO
        </a>
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
            <p class="mt-3 text-muted">No tienes pedidos aún.</p>
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
                        <span class="order-date"><i class="bi bi-calendar"></i> {{ $order->created_at->format('d/m/Y') }}</span>
                    </div>
                    <span class="status-badge status-{{ $order->status }}">
                        <i class="bi bi-circle-fill" style="font-size: 0.35rem;"></i>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="order-items">
                    @foreach($order->items as $item)
                        <div class="order-item">
                            <i class="bi bi-cup-straw"></i> {{ $item->product_name }} x{{ $item->quantity }} – S/. {{ number_format($item->subtotal, 2) }}
                        </div>
                    @endforeach
                </div>

                <div class="order-details">
                    @if($order->delivery_type === 'pickup')
                        <div><i class="bi bi-shop"></i> Recojo en tienda</div>
                    @else
                        <div><i class="bi bi-geo-alt"></i> {{ $order->delivery_address }}</div>
                    @endif
                    @if($order->delivery_reference)
                        <div><i class="bi bi-pin"></i> {{ $order->delivery_reference }}</div>
                    @endif
                </div>

                <div class="order-total">TOTAL: S/. {{ number_format($order->total, 2) }}</div>

                <div class="order-actions">
                    <a href="{{ route('orders.show', $order) }}" class="btn-retro-primary btn-sm">
                        <i class="bi bi-eye"></i> VER DETALLE
                    </a>

                    @if($order->canBeCancelledByUser())
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-retro-danger btn-sm" onclick="return confirm('¿Cancelar este pedido?')">
                                <i class="bi bi-x-circle"></i> CANCELAR
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
