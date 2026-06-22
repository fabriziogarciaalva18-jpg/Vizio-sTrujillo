@extends('layouts.retro')

@section('title', 'Mi Carrito - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← MI CARRITO →</h1>
        <div class="section-divider"></div>
    </div>

    @if(session('success'))
    <div class="alert alert-retro" style="background: #DCFCE7; color: #166534;">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B;">
        {{ session('error') }}
    </div>
    @endif

    @php
        $cart = session()->get('cart', []);
        $total = 0;
    @endphp

    @if(empty($cart))
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 4rem; color: #9E9890;"></i>
        <p class="mt-3 text-muted">Tu carrito está vacío</p>
        <a href="{{ route('catalog') }}" class="btn-retro-primary mt-2">
            <i class="bi bi-grid-3x3-gap-fill"></i> VER CATÁLOGO
        </a>
    </div>
    @else
    <div class="row">
        <div class="col-lg-8">
            @foreach($cart as $id => $item)
            @php $itemTotal = $item['price'] * $item['quantity']; $total += $itemTotal; @endphp
            <div class="order-card mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0">{{ $item['name'] }}</h4>
                        <small class="text-muted">S/. {{ number_format($item['price'], 2) }} c/u</small>
                    </div>
                    <div class="col-md-3">
                        <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99" style="width: 60px; margin-right: 5px;" class="form-control form-control-sm">
                            <button type="submit" class="btn-retro-secondary btn-sm">ACT</button>
                        </form>
                    </div>
                    <div class="col-md-2">
                        <strong>S/. {{ number_format($itemTotal, 2) }}</strong>
                    </div>
                    <div class="col-md-1">
                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-retro-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-lg-4">
            <div class="profile-card">
                <h3 class="profile-section-title">RESUMEN</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>S/. {{ number_format($total , 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Envío</span>
                    <span>S/. 8.00</span>
                </div>
                <hr>
                <div class="summary-row total">
                    <span><strong>TOTAL</strong></span>
                    <span><strong>S/. {{ number_format(($total + 8.00) , 2) }}</strong></span>
                </div>
                <a href="{{ route('checkout') }}" class="btn-retro-primary w-100 mt-3">
                    <i class="bi bi-credit-card"></i> PROCEDER AL PAGO
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function updateCart() {
        location.reload();
    }

    document.querySelectorAll('.cart-qty').forEach(input => {
        input.addEventListener('change', function() {
            const key = this.dataset.key;
            const quantity = this.value;

            fetch(`/cart/update/${key}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) updateCart();
            });
        });
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.dataset.key;
            if (confirm('¿Eliminar este producto del carrito?')) {
                window.location.href = `/cart/remove/${key}`;
            }
        });
    });

    document.querySelectorAll('.decrement').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.dataset.key;
            const input = document.querySelector(`.cart-qty[data-key="${key}"]`);
            if (input && input.value > 1) {
                input.value = parseInt(input.value) - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    document.querySelectorAll('.increment').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.dataset.key;
            const input = document.querySelector(`.cart-qty[data-key="${key}"]`);
            if (input) {
                input.value = parseInt(input.value) + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
@endpush
