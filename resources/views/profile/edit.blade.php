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
                    <div class="avatar-wrapper mx-auto" id="avatarPreview" style="cursor: pointer; width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 2px solid var(--gray-200); position: relative;" onclick="document.getElementById('avatarInput').click();">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="avatar-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; border-radius: 50%;">
                            <i class="bi bi-camera-fill" style="font-size: 2rem; color: white;"></i>
                        </div>
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

                <!-- Hover effect para el avatar -->
                <style>
                    #avatarPreview:hover .avatar-overlay {
                        opacity: 1;
                    }
                </style>

                <div class="profile-stats mt-4">
                    <div class="row g-2 justify-content-center">
                        <div class="col-6">
                            <div class="stat-box text-center p-3">
                                <span class="stat-number d-block fs-3 fw-bold">{{ auth()->user()->paid_orders_count ?? 0 }}</span>
                                <span class="stat-label text-muted small text-uppercase">PEDIDOS</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box text-center p-3">
                                <span class="stat-number d-block fs-3 fw-bold">S/. {{ number_format(auth()->user()->total_spent ?? 0, 0) }}</span>
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
            <!-- DATOS PERSONALES -->
<div class="profile-card mb-4">
    <h3 class="profile-section-title">
        <i class="bi bi-person-badge"></i> DATOS PERSONALES
    </h3>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">NOMBRE COMPLETO *</label>
                <input type="text" name="name" class="form-control form-control-retro"
                       value="{{ old('name', auth()->user()->name) }}" required>
                @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">CORREO ELECTRÓNICO *</label>
                <input type="email" name="email" class="form-control form-control-retro"
                       value="{{ old('email', auth()->user()->email) }}" required>
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">TELÉFONO</label>
                <input type="tel" name="phone" class="form-control form-control-retro"
                       value="{{ old('phone', auth()->user()->phone) }}"
                       placeholder="Ej: 987654321 (9 dígitos)">
                @error('phone')
                    <span class="text-danger small">{{ $message }}</span>
                @else
                    <small class="text-muted">Formato: 9 dígitos (ej: 987654321)</small>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">DIRECCIÓN</label>
                <input type="text" name="address" class="form-control form-control-retro"
                       value="{{ old('address', auth()->user()->address) }}"
                       placeholder="Calle, número, urbanización, distrito">
                @error('address')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-4 text-center text-md-start">
            <button type="submit" class="btn-retro-primary">
                <i class="bi bi-save"></i> GUARDAR CAMBIOS
            </button>
        </div>
    </form>
</div>

<!-- SEGURIDAD -->
<div class="profile-card mb-4">
    <h3 class="profile-section-title">
        <i class="bi bi-lock"></i> SEGURIDAD
    </h3>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">CONTRASEÑA ACTUAL *</label>
                <input type="password" name="current_password" class="form-control form-control-retro" required>
                @error('current_password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">NUEVA CONTRASEÑA *</label>
                <input type="password" name="password" class="form-control form-control-retro" required>
                <small class="text-muted">Mínimo 8 caracteres, incluir mayúscula, minúscula, número y símbolo.</small>
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">CONFIRMAR CONTRASEÑA *</label>
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
            <!-- ELIMINAR CUENTA -->
            <div class="profile-card" style="border: 1px solid #FCA5A5;">
                <h3 class="profile-section-title" style="color: #991B1B;">
                    <i class="bi bi-exclamation-triangle"></i> ZONA DE PELIGRO
                </h3>

                <p class="text-muted small">
                    <i class="bi bi-info-circle"></i>
                    Al desactivar tu cuenta, tu perfil se marcará como inactivo.
                    Podrás reactivarlo contactando al soporte.
                </p>

                <button class="btn-retro-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-person-x"></i> DESACTIVAR CUENTA
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN PARA ELIMINAR CUENTA -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-retro">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel" style="color: #991B1B;">
                    <i class="bi bi-exclamation-triangle-fill"></i> DESACTIVAR CUENTA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-person-x" style="font-size: 4rem; color: #991B1B;"></i>
                <p class="mt-3">
                    ¿Estás seguro de que deseas desactivar tu cuenta?
                </p>
                <p class="text-muted small">
                    <strong>Esta acción es irreversible.</strong><br>
                    Todos tus datos personales se mantendrán en nuestra base de datos<br>
                    pero tu cuenta quedará inactiva y no podrás iniciar sesión.
                </p>
                <div class="alert alert-retro mt-3" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                    <i class="bi bi-info-circle"></i> Para reactivar tu cuenta, contacta a soporte@vizio.pe
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-retro-secondary" data-bs-dismiss="modal">CANCELAR</button>
                <form action="{{ route('profile.destroy') }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-retro-danger" onclick="return confirm('¿Estás completamente seguro de desactivar tu cuenta?')">
                        <i class="bi bi-person-x"></i> DESACTIVAR CUENTA
                    </button>
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

        // Validar tamaño
        if (file.size > 2 * 1024 * 1024) {
            showNotification('La imagen no debe superar los 2MB', 'error');
            this.value = '';
            return;
        }

        // Validar tipo
        if (!file.type.startsWith('image/')) {
            showNotification('Solo se permiten archivos de imagen', 'error');
            this.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('avatar', file);

        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> SUBIENDO...';

        fetch('{{ route("avatar.upload") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                avatarPreview.querySelector('img').src = data.avatar_url + '?t=' + Date.now();
                removeBtn.style.display = 'inline-block';
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Error al subir imagen', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al subir la imagen: ' + error.message, 'error');
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="bi bi-camera"></i> CAMBIAR';
            avatarInput.value = '';
        });
    });

    // Remove avatar
    removeBtn.addEventListener('click', () => {
        if (!confirm('¿Eliminar tu foto de perfil?')) return;

        removeBtn.disabled = true;
        removeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        fetch('{{ route("avatar.remove") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                avatarPreview.querySelector('img').src = data.avatar_url + '?t=' + Date.now();
                removeBtn.style.display = 'none';
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al eliminar la imagen', 'error');
        })
        .finally(() => {
            removeBtn.disabled = false;
            removeBtn.innerHTML = '<i class="bi bi-trash"></i> ELIMINAR';
        });
    });

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.style.cssText = `position: fixed; bottom: 20px; right: 20px; z-index: 9999; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; background: ${type === 'success' ? '#DCFCE7' : '#FEE2E2'}; color: ${type === 'success' ? '#166534' : '#991B1B'}; box-shadow: 0 2px 10px rgba(0,0,0,0.1); animation: fadeIn 0.3s ease;`;
        notification.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill') + '"></i> ' + message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    // CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
@endsection
