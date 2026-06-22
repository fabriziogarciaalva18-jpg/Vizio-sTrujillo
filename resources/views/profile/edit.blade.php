@extends('layouts.retro')

@section('title', 'Mi Perfil - Vizio\'s')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="section-title" style="font-size: 1.8rem;">← MI PERFIL →</h1>
        <div class="section-divider"></div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-4 mb-4">
            <div class="profile-card text-center h-100">
                <!-- AVATAR CON UPLOAD -->
                <div class="avatar-container">
                    <div class="avatar-wrapper mx-auto" id="avatarPreview" style="cursor: pointer; width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 2px solid var(--gray-200);" onclick="document.getElementById('avatarInput').click();">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                    <div class="mt-3 d-flex justify-content-center gap-2">
                        <button class="btn-retro-secondary btn-sm" id="uploadAvatarBtn">
                            <i class="bi bi-camera"></i> CAMBIAR
                        </button>
                        <button class="btn-retro-danger btn-sm" id="removeAvatarBtn" style="{{ auth()->user()->avatar ? '' : 'display: none;' }}">
                            <i class="bi bi-trash"></i> ELIMINAR
                        </button>
                    </div>
                </div>
                
                <div class="profile-stats mt-4">
                    <div class="row g-2 justify-content-center">
                        <div class="col-6">
                            <div class="stat-box text-center p-3">
                                <span class="stat-number d-block fs-3 fw-bold">{{ auth()->user()->paid_orders_count }}</span>
                                <span class="stat-label text-muted small text-uppercase">PEDIDOS</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box text-center p-3">
                                <span class="stat-number d-block fs-3 fw-bold">S/. {{ number_format(auth()->user()->total_spent / 100, 0) }}</span>
                                <span class="stat-label text-muted small text-uppercase">GASTADO</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="member-since mt-3 pt-2 border-top">
                    <i class="bi bi-calendar-check"></i> Miembro desde {{ auth()->user()->created_at->format('M Y') }}
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="profile-card mb-4">
                <h3 class="profile-section-title">
                    <i class="bi bi-person-badge"></i> DATOS PERSONALES
                </h3>
                
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NOMBRE COMPLETO</label>
                            <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">CORREO ELECTRÓNICO</label>
                            <input type="email" name="email" class="form-control form-control-retro" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">TELÉFONO</label>
                            <input type="tel" name="phone" class="form-control form-control-retro" value="{{ old('phone', auth()->user()->phone) }}" placeholder="Ej: 987654321">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">DIRECCIÓN</label>
                            <input type="text" name="address" class="form-control form-control-retro" value="{{ old('address', auth()->user()->address) }}" placeholder="Tu dirección de entrega">
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center text-md-start">
                        <button type="submit" class="btn-retro-primary">
                            <i class="bi bi-save"></i> GUARDAR CAMBIOS
                        </button>
                    </div>
                </form>
            </div>

            <div class="profile-card">
                <h3 class="profile-section-title">
                    <i class="bi bi-lock"></i> SEGURIDAD
                </h3>
                
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">CONTRASEÑA ACTUAL</label>
                            <input type="password" name="current_password" class="form-control form-control-retro" required>
                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NUEVA CONTRASEÑA</label>
                            <input type="password" name="password" class="form-control form-control-retro" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CONFIRMAR CONTRASEÑA</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-retro" required>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center text-md-start">
                        <button type="submit" class="btn-retro-primary">
                            <i class="bi bi-key"></i> CAMBIAR CONTRASEÑA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Upload avatar
    const avatarInput = document.getElementById('avatarInput');
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    const removeBtn = document.getElementById('removeAvatarBtn');
    const avatarPreview = document.getElementById('avatarPreview');

    uploadBtn.addEventListener('click', () => {
        avatarInput.click();
    });

    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('avatar', file);

        fetch('{{ route("avatar.upload") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                avatarPreview.querySelector('img').src = data.avatar_url + '?t=' + Date.now();
                removeBtn.style.display = 'inline-block';
                showNotification(data.message);
            } else {
                showNotification(data.message || 'Error al subir imagen', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al subir la imagen', 'error');
        });
    });

    removeBtn.addEventListener('click', () => {
        if (confirm('¿Eliminar tu foto de perfil?')) {
            fetch('{{ route("avatar.remove") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    avatarPreview.querySelector('img').src = data.avatar_url;
                    removeBtn.style.display = 'none';
                    showNotification(data.message);
                }
            });
        }
    });

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.style.cssText = `position: fixed; bottom: 20px; right: 20px; z-index: 9999; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; background: ${type === 'success' ? '#DCFCE7' : '#FEE2E2'}; color: ${type === 'success' ? '#166534' : '#991B1B'}; box-shadow: 0 2px 10px rgba(0,0,0,0.1);`;
        notification.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill') + '"></i> ' + message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
</script>
@endpush
@endsection