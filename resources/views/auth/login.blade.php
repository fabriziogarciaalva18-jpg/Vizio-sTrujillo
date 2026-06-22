@extends('layouts.retro')

@section('title', 'Iniciar Sesión - Vizio\'s')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-lg-5 col-md-7">
            <div class="profile-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <!-- Logo de la empresa -->
                    <img src="{{ asset('assets/tu-logo.png') }}" alt="Vizio's" height="60" class="mb-3">
                    <h2 class="login-title" style="font-family: 'DM Serif Display', serif; font-size: 1.5rem; letter-spacing: -0.5px;">
                        ← ACCESO →
                    </h2>
                    <div class="title-divider" style="width: 40px; height: 2px; background: var(--gray-300); margin: 0.5rem auto;"></div>
                </div>

                <!-- Mensaje de sesión -->
                @if(session('status'))
                    <div class="alert alert-retro mb-3" style="background: #DCFCE7; color: #166534; border: 1px solid #86EFAC;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope me-1"></i> CORREO ELECTRÓNICO</label>
                        <input type="email" name="email" class="form-control form-control-retro" value="{{ old('email') }}" required autofocus placeholder="tu@correo.com">
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock me-1"></i> CONTRASEÑA</label>
                        <input type="password" name="password" class="form-control form-control-retro" required placeholder="••••••••">
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Recordarme y Olvidé contraseña -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">RECORDAR SESIÓN</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="link-retro" style="font-size: 0.75rem;">
                                <i class="bi bi-question-circle"></i> ¿OLVIDASTE TU CONTRASEÑA?
                            </a>
                        @endif
                    </div>

                    <!-- Botón -->
                    <button type="submit" class="btn-retro-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i> INICIAR SESIÓN
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">¿Aún no tienes cuenta?
                        <a href="{{ route('register') }}" class="link-retro" style="font-weight: 600;">REGÍSTRATE</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
