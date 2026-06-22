@extends('layouts.retro')

@section('title', 'Pago Enviado - Vizio\'s')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="profile-card text-center">
                <i class="bi bi-check-circle-fill" style="font-size: 5rem; color: #166534;"></i>
                <h2 class="mt-3">¡Comprobante Enviado!</h2>
                <p>Hemos recibido tu comprobante de pago.</p>
                <div class="alert alert-retro mt-3" style="background: #FEF3C7;">
                    <i class="bi bi-clock-history"></i> Revisaremos tu pago en las próximas 24 horas.
                </div>
                <p class="text-muted">Te notificaremos por correo cuando sea confirmado.</p>
                <div class="mt-4">
                    <a href="{{ route('orders.show', $order) }}" class="btn-retro-primary">
                        <i class="bi bi-eye"></i> VER ESTADO DEL PEDIDO
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection