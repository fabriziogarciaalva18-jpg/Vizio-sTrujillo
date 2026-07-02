@extends('layouts.retro')

@section('title', 'Editar Personalización')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="section-title" style="font-size: 1.5rem;">← EDITAR PERSONALIZACIÓN →</h1>
        <div class="section-divider"></div>
    </div>

    @if ($errors->any())
        <div class="alert alert-retro mb-4" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; border-radius: 8px; padding: 1rem;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                <form action="{{ route('admin.customizations.update', $customization) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Producto -->
                        <div class="col-md-6">
                            <label class="form-label">Producto *</label>
                            <select name="product_id" class="form-select form-control-retro" required>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', $customization->product_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">¿Para qué producto será esta opción de personalización?</small>
                        </div>

                        <!-- Tipo de personalización -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de personalización *</label>
                            <select name="config_type" class="form-select form-control-retro" required>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('config_type', $customization->config_type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Ej: Tamaño, Sabor, Relleno, Cobertura, Pisos...</small>
                        </div>

                        <!-- Nombre de la opción -->
                        <div class="col-md-6">
                            <label class="form-label">Nombre de la opción *</label>
                            <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name', $customization->name) }}" required placeholder="Ej: Grande, Chocolate, Tres Leches, Fondant...">
                        </div>

                        <!-- Precio adicional -->
                        <div class="col-md-6">
                            <label class="form-label">Precio adicional</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: var(--gray-50);">S/.</span>
                                <input type="number" step="0.01" name="price_modifier" class="form-control form-control-retro" value="{{ old('price_modifier', $customization->price_modifier) }}" placeholder="0.00">
                            </div>
                            <small class="text-muted">Costo extra que se sumará al precio base del producto.</small>
                        </div>

                        <!-- Orden de aparición -->
                        <div class="col-md-6">
                            <label class="form-label">Orden de aparición</label>
                            <input type="number" name="sort_order" class="form-control form-control-retro" value="{{ old('sort_order', $customization->sort_order) }}" placeholder="0 = primero">
                            <small class="text-muted">Define el orden en que se mostrará esta opción entre las demás.</small>
                        </div>

                        <!-- Activo -->
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $customization->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activa</label>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.customizations.index') }}" class="btn-retro-secondary me-2">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-retro-primary">
                            <i class="bi bi-save"></i> Actualizar personalización
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
