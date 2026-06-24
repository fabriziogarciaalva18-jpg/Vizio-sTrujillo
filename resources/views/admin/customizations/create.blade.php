@extends('layouts.retro')

@section('title', 'Crear Personalización')

@section('content')
<div class="container py-4">
    <h1 class="section-title" style="font-size: 1.5rem;">← CREAR PERSONALIZACIÓN →</h1>
    <div class="section-divider"></div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="profile-card">
                <form action="{{ route('admin.customizations.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Producto *</label>
                        <select name="product_id" class="form-select form-control-retro" required>
                            <option value="">Selecciona un producto</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">¿Para qué producto será esta opción de personalización?</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de personalización *</label>
                        <select name="config_type" class="form-select form-control-retro" required>
                            <option value="">Selecciona un tipo</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('config_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Ej: Tamaño, Sabor, Relleno, Cobertura, Pisos...</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre de la opción *</label>
                        <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name') }}" required placeholder="Ej: Grande, Chocolate, Tres Leches, Fondant...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio adicional</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: var(--gray-50);">S/.</span>
                            <input type="number" step="0.01" name="price_modifier" class="form-control form-control-retro" value="{{ old('price_modifier', 0) }}" placeholder="0.00">
                        </div>
                        <small class="text-muted">Costo extra que se sumará al precio base del producto.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Orden de aparición</label>
                        <input type="number" name="sort_order" class="form-control form-control-retro" value="{{ old('sort_order', 0) }}" placeholder="0 = primero">
                        <small class="text-muted">Define el orden en que se mostrará esta opción entre las demás.</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Activa</label>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.customizations.index') }}" class="btn-retro-secondary">Cancelar</a>
                        <button type="submit" class="btn-retro-primary">Guardar personalización</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
