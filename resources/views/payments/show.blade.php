@extends('layouts.retro')

@section('title', 'Realizar Pago - Vizio\'s')
>
@endif
@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← REALIZAR PAGO →</h1>
        <div class="section-divider"></div>
    </div>

    @if ($errors->any())
    <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div

    <div class="row">
        <div class="col-lg-6">
            <div class="profile-card">
                <h3 class="profile-section-title">
                    <i class="bi bi-credit-card"></i> {{ strtoupper($method) }}
                </h3>

                @if($method == 'yape')
                    @include('payments.partials.yape')
                @elseif($method == 'plin')
                    @include('payments.partials.plin')
                @elseif($method == 'transferencia')
                    @include('payments.partials.transferencia')
                @endif

                <div class="mt-4">
                    <p><strong>Total a pagar:</strong> <span class="text-success fw-bold">S/. {{ number_format($total, 2) }}</span></p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="profile-card">
                <h3 class="profile-section-title">
                    <i class="bi bi-receipt"></i> RESUMEN DEL PEDIDO
                </h3>

                @foreach($cart as $item)
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                    <span>S/. {{ number_format(($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'], 2) }}</span>
                </div>
                @endforeach

                <hr>
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span>S/. {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-truck"></i> Envío</span>
                    <span>S/. {{ number_format($deliveryFee, 2) }}</span>
                </div>

                @if(!empty($checkoutData['delivery_distance']))
                <div class="d-flex justify-content-between text-muted small">
                    <span><i class="bi bi-rulers"></i> Distancia</span>
                    <span>{{ number_format($checkoutData['delivery_distance'], 1) }} km</span>
                </div>
                @endif

                @if(!empty($checkoutData['delivery_reference']))
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-pin"></i> Referencia</span>
                    <span class="text-muted small">{{ $checkoutData['delivery_reference'] }}</span>
                </div>
                @endif

                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>TOTAL</span>
                    <span class="text-success">S/. {{ number_format($total, 2) }}</span>
                </div>

                <hr>

                <form action="{{ route('payment.voucher') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-hash"></i> NÚMERO DE REFERENCIA / OPERACIÓN</label>
                        <input type="text" name="payment_reference" class="form-control form-control-retro" required placeholder="Ej: 1234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-image"></i> CAPTURA O COMPROBANTE DE PAGO</label>
                        <input type="file" name="voucher" class="form-control form-control-retro" accept="image/*" required>
                        <small class="text-muted">Sube una captura de pantalla del pago realizado</small>
                    </div>
                    <button type="submit" class="btn-retro-primary w-100">
                        <i class="bi bi-upload"></i> ENVIAR COMPROBANTE
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
