@extends('layouts.retro')

@section('title', 'Crear Producto')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← CREAR PRODUCTO →</h1>
        <div class="section-divider"></div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 8px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="profile-card">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Categoría *</label>
                            <select name="category_id" class="form-select form-control-retro" required>
                                <option value="">Selecciona</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción *</label>
                            <textarea name="description" rows="4" class="form-control form-control-retro" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Precio (S/.) *</label>
                            <input type="number" step="0.01" name="base_price" class="form-control form-control-retro" value="{{ old('base_price') }}" required placeholder="Ej: 350.00">
                            <small class="text-muted">Ingresa el precio en soles, ejemplo: 350.00</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo de producto *</label>
                            <select name="product_type" class="form-select form-control-retro" required>
                                <option value="simple" {{ old('product_type') == 'simple' ? 'selected' : '' }}>Simple</option>
                                <option value="configurable" {{ old('product_type') == 'configurable' ? 'selected' : '' }}>Configurable</option>
                                <option value="catering" {{ old('product_type') == 'catering' ? 'selected' : '' }}>Catering</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="image_url" class="form-control form-control-retro" accept="image/*">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activo (visible en catálogo)</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr>
                            <h5>Opciones de personalización</h5>
                            <div class="form-check">
                                <input type="checkbox" name="has_sizes" id="has_sizes" {{ old('has_sizes') ? 'checked' : '' }}>
                                <label for="has_sizes">Tamaños</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_layers" id="has_layers" {{ old('has_layers') ? 'checked' : '' }}>
                                <label for="has_layers">Pisos</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_flavors" id="has_flavors" {{ old('has_flavors') ? 'checked' : '' }}>
                                <label for="has_flavors">Sabores</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_fillings" id="has_fillings" {{ old('has_fillings') ? 'checked' : '' }}>
                                <label for="has_fillings">Rellenos</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_coverings" id="has_coverings" {{ old('has_coverings') ? 'checked' : '' }}>
                                <label for="has_coverings">Coberturas</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.products.index') }}" class="btn-retro-secondary">Cancelar</a>
                        <button type="submit" class="btn-retro-primary">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
