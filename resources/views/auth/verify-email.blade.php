@extends('layouts.retro')

@section('title', 'Verificar Correo - Vizio\'s')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-lg-6 col-md-8">
            <div class="profile-card p-5 text-center">
                <div class="mb-4">
                    <i class="bi bi-envelope-check" style="font-size: 4rem; color: #166534;"></i>
                </div>
                <h2 class="mb-3" style="font-family: 'DM Serif Display', serif;">← VERIFICA TU CORREO →</h2>
                <div class="title-divider" style="width: 40px; height: 2px; background: var(--gray-300); margin: 0.5rem auto;"></div>

                <p class="text-muted mt-3">
                    Te hemos enviado un enlace de verificación a <strong>{{ auth()->user()->email }}</strong>.
                    Haz clic en el enlace para activar tu cuenta.
                </p>

                <div class="alert alert-retro mt-4" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                    <i class="bi bi-info-circle"></i> Si no recibiste el correo, revisa tu carpeta de spam o solicita uno nuevo.
                </div>

                <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-retro-secondary">
                        <i class="bi bi-arrow-repeat"></i> REENVIAR ENLACE
                    </button>
                </form>

                <div class="mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-retro-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> CERRAR SESIÓN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
