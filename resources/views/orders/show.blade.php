@extends('layouts.retro')

@section('title', 'Pedido #' . $order->order_number)

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← DETALLE DEL PEDIDO →</h1>
        <div class="section-divider"></div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="profile-card">
                <h3 class="profile-section-title">PEDIDO #{{ $order->order_number }}</h3>
                <p><strong>Estado:</strong>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </p>
                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>

                <hr>

                <h4>Productos</h4>
                <table class="admin-table">
                    <thead>
                        <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>S/. {{ number_format($item->unit_price , 2) }}</td>
                            <td>S/. {{ number_format($item->subtotal , 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <hr>

                <p><strong>Dirección:</strong> {{ $order->delivery_address }}</p>
                <p><strong>Distrito:</strong> {{ $order->district }}</p>
                <p><strong>Teléfono:</strong> {{ $order->phone }}</p>
                <p><strong>Método de pago:</strong> {{ strtoupper($order->payment_method) }}</p>
                @if($order->special_instructions)
                <p><strong>Instrucciones:</strong> {{ $order->special_instructions }}</p>
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="profile-card">
                <h3 class="profile-section-title">RESUMEN</h3>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>S/. {{ number_format($order->subtotal , 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Envío</span>
                    <span>S/. {{ number_format($order->delivery_fee , 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>TOTAL</span>
                    <span>S/. {{ number_format($order->total , 2) }}</span>
                </div>

                @if($order->status == 'pending')
                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-retro-danger w-100" onclick="return confirm('¿Cancelar este pedido?')">
                        <i class="bi bi-x-circle"></i> CANCELAR PEDIDO
                    </button>
                </form>
                @endif

                <a href="{{ route('orders.index') }}" class="btn-retro-secondary w-100 mt-2">
                    <i class="bi bi-arrow-left"></i> MIS PEDIDOS
                </a>
            </div>
        </div>
    </div>
</div>
<div class="mt-4 text-center">
    <form action="{{ route('payment.cancel', $order) }}" method="POST" onsubmit="return confirm('¿Cancelar este pedido? Los productos volverán a tu carrito.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-retro-danger">
            <i class="bi bi-x-circle"></i> CANCELAR PEDIDO
        </button>
    </form>
    <a href="{{ route('cart') }}" class="btn-retro-secondary mt-2 d-inline-block">
        <i class="bi bi-arrow-left"></i> VOLVER AL CARRITO
    </a>
</div>
@endsection
