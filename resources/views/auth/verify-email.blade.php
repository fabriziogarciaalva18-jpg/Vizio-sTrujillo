@extends('layouts.retro')

@section('title', 'Verificar Correo - Vizio\'s')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-lg-6 col-md-8">
            <div class="profile-card p-5 text-center">
                <!-- Icono -->
                <div class="mb-4">
                    <i class="bi bi-envelope-check" style="font-size: 4rem; color: #166534;"></i>
                </div>

                <!-- Título -->
                <h2 class="mb-3" style="font-family: 'DM Serif Display', serif; font-size: 1.8rem;">
                    ← VERIFICA TU CORREO →
                </h2>
                <div class="title-divider" style="width: 40px; height: 2px; background: var(--gray-300); margin: 0.5rem auto;"></div>

                <!-- Mensaje principal -->
                <p class="text-muted mt-3" style="font-size: 0.95rem; line-height: 1.7;">
                    Para poder realizar compras, agregar productos al carrito y acceder a todas las funcionalidades,
                    necesitas verificar tu dirección de correo electrónico.
                </p>

                <p class="text-muted mt-2" style="font-size: 0.9rem;">
                    Te hemos enviado un enlace de verificación a 
                    <strong style="color: var(--black);">{{ auth()->user()->email }}</strong>.
                </p>

                <!-- Alerta -->
                <div class="alert alert-retro mt-4" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; border-radius: 8px; font-size: 0.85rem;">
                    <i class="bi bi-info-circle me-2"></i> 
                    Si no recibiste el correo, revisa tu carpeta de <strong>SPAM</strong> o solicita uno nuevo.
                </div>

                <!-- Botón reenviar -->
                <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-retro-secondary" style="padding: 0.6rem 2rem;">
                        <i class="bi bi-arrow-repeat me-2"></i> REENVIAR ENLACE
                    </button>
                </form>

                @if(session('success'))
                    <div class="mt-3 alert alert-success" style="background: #DCFCE7; color: #166534; border: 1px solid #86EFAC; border-radius: 8px; font-size: 0.85rem; padding: 0.5rem 1rem;">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-3 alert alert-danger" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; border-radius: 8px; font-size: 0.85rem; padding: 0.5rem 1rem;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Cerrar sesión -->
                <div class="mt-4 pt-3 border-top border-light">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-retro-danger btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i> CERRAR SESIÓN
                        </button>
                    </form>
                </div>

                <!-- Ayuda adicional -->
                <div class="mt-3">
                    <p class="text-muted small" style="font-size: 0.7rem;">
                        <i class="bi bi-question-circle"></i> 
                        ¿Problemas? Contacta a 
                        <a href="mailto:soporte@vizio.pe" class="link-retro" style="font-weight: 600;">soporte@vizio.pe</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection