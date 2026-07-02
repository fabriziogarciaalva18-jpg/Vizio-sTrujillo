@extends('layouts.retro')

@section('title', 'Editar Personalización')

@section('content')
<div class="container py-4">
    <h1 class="section-title" style="font-size: 1.5rem;">← EDITAR PERSONALIZACIÓN →</h1>
    <div class="section-divider"></div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                <form action="{{ route('admin.customizations.update', $customization) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de personalización *</label>
                            <select name="config_type" class="form-select form-control-retro" required>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ $customization->config_type == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Nombre de la opción *</label>
                            <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name', $customization->name) }}" required>
                        </div>

                        <!-- Precio adicional -->
                        <div class="col-md-4">
                            <label class="form-label">Precio adicional (S/.)</label>
                            <input type="number" step="0.01" name="price_modifier" class="form-control form-control-retro" value="{{ old('price_modifier', $customization->price_modifier) }}">
                        </div>

                        <!-- Orden -->
                        <div class="col-md-4">
                            <label class="form-label">Orden de aparición</label>
                            <input type="number" name="sort_order" class="form-control form-control-retro" value="{{ old('sort_order', $customization->sort_order) }}">
                        </div>

                        <!-- Activo -->
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $customization->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activa</label>
                            </div>
                        </div>

                        <!-- SELECCIÓN MÚLTIPLE DE PRODUCTOS -->
                        <div class="col-12">
                            <label class="form-label"><i class="bi bi-box-seam"></i> Productos aplicables *</label>
                            @php
                                $selectedProducts = $customization->products->pluck('id')->toArray();
                            @endphp
                            <select name="product_ids[]" class="form-select form-control-retro" multiple required style="min-height: 120px;">
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ in_array($p->id, old('product_ids', $selectedProducts)) ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mantén presionada la tecla Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples productos.</small>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.customizations.index') }}" class="btn-retro-secondary">Cancelar</a>
                        <button type="submit" class="btn-retro-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
