@extends('layouts.retro')

@section('title', 'Checkout - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← FINALIZAR PEDIDO →</h1>
        <div class="section-divider"></div>
    </div>

    @php
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $delivery_fee = 8;
        $total = $subtotal + $delivery_fee;
    @endphp

    @if(empty($cart))
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 4rem;"></i>
        <p>Tu carrito está vacío</p>
        <a href="{{ route('catalog') }}" class="btn-retro-primary">VER CATÁLOGO</a>
    </div>
    @else
    <div class="row">
        <div class="col-lg-7">
            <div class="profile-card">
                <h3 class="profile-section-title">DATOS DE ENTREGA</h3>
                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">DIRECCIÓN DE ENTREGA *</label>
                            <input type="text" name="delivery_address" class="form-control form-control-retro" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DISTRITO *</label>
                            <input type="text" name="district" class="form-control form-control-retro" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">TELÉFONO *</label>
                            <input type="text" name="phone" class="form-control form-control-retro" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">FECHA DE ENTREGA *</label>
                            <input type="date" name="delivery_date" class="form-control form-control-retro" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">MÉTODO DE PAGO *</label>
                            <select name="payment_method" class="form-select form-control-retro" required>
                                <option value="yape">YAPE</option>
                                <option value="plin">PLIN</option>
                                <option value="transferencia">TRANSFERENCIA BANCARIA</option>
                                <option value="contraentrega">CONTRA ENTREGA</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">INSTRUCCIONES ESPECIALES</label>
                            <textarea name="special_instructions" class="form-control form-control-retro" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn-retro-primary w-100 py-2">
                            <i class="bi bi-credit-card"></i> CONTINUAR AL PAGO
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="profile-card">
                <h3 class="profile-section-title">RESUMEN</h3>
                @foreach($cart as $item)
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                    <span>S/. {{ number_format(($item['price'] * $item['quantity']) , 2) }}</span>
                </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span>S/. {{ number_format($subtotal , 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Envío</span>
                    <span>S/. {{ number_format($delivery_fee , 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>TOTAL</span>
                    <span class="text-success">S/. {{ number_format($total , 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
