@extends('layouts.retro')

@section('title', 'Registrarse - Vizio\'s')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-lg-6 col-md-8">
            <div class="profile-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/tu-logo.png') }}" alt="Vizio's" height="60" class="mb-3">
                    <h2 class="login-title" style="font-family: 'DM Serif Display', serif; font-size: 1.5rem; letter-spacing: -0.5px;">
                        ← REGÍSTRATE →
                    </h2>
                    <div class="title-divider" style="width: 40px; height: 2px; background: var(--gray-300); margin: 0.5rem auto;"></div>
                    <p class="text-muted small">Crea tu cuenta y empieza a disfrutar de nuestros dulces.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person me-1"></i> NOMBRE COMPLETO</label>
                        <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name') }}" required autofocus placeholder="Ej: Ana González">
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope me-1"></i> CORREO ELECTRÓNICO</label>
                        <input type="email" name="email" class="form-control form-control-retro" value="{{ old('email') }}" required placeholder="tu@correo.com">
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-lock me-1"></i> CONTRASEÑA</label>
                            <input type="password" name="password" class="form-control form-control-retro" required placeholder="••••••••">
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-check-circle me-1"></i> CONFIRMAR</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-retro" required placeholder="••••••••">
                            @error('password_confirmation')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- CAPTCHA -->
                    <div class="mb-3">
                        @php
                            $siteKey = config('services.recaptcha.site_key') ?? env('RECAPTCHA_SITE_KEY');
                        @endphp
                        <div class="g-recaptcha" data-sitekey="{{ $siteKey }}"></div>
                        @error('g-recaptcha-response')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Botón -->
                    <button type="submit" class="btn-retro-primary w-100 py-2 mt-2">
                        <i class="bi bi-person-plus me-2"></i> CREAR CUENTA
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">¿Ya tienes cuenta?
                        <a href="{{ route('login') }}" class="link-retro" style="font-weight: 600;">INICIA SESIÓN</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js?hl=es" async defer></script>
@endpush
