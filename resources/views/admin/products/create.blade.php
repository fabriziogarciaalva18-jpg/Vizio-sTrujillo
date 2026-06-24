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
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name') }}" required>
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6">
                            <label class="form-label">Categoría *</label>
                            <select name="category_id" class="form-select form-control-retro" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label">Descripción *</label>
                            <textarea name="description" rows="4" class="form-control form-control-retro" required>{{ old('description') }}</textarea>
                        </div>

                        <!-- Precio -->
                        <div class="col-md-4">
                            <label class="form-label">Precio (S/.) *</label>
                            <input type="number" step="0.01" name="base_price" class="form-control form-control-retro" value="{{ old('base_price') }}" required placeholder="Ej: 350.00">
                            <small class="text-muted">Ingresa el precio en soles, ejemplo: 350.00</small>
                        </div>

                        <!-- Tipo de producto -->
                        <div class="col-md-4">
                            <label class="form-label">Tipo de producto *</label>
                            <select name="product_type" class="form-select form-control-retro" required>
                                <option value="simple" {{ old('product_type') == 'simple' ? 'selected' : '' }}>Simple</option>
                                <option value="configurable" {{ old('product_type') == 'configurable' ? 'selected' : '' }}>Configurable (tortas)</option>
                                <option value="catering" {{ old('product_type') == 'catering' ? 'selected' : '' }}>Catering</option>
                            </select>
                        </div>

                        <!-- Imagen -->
                        <div class="col-md-4">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="image_url" class="form-control form-control-retro" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG (max 2MB)</small>
                        </div>

                        <!-- Activo -->
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activo (visible en el catálogo)</label>
                            </div>
                        </div>

                        <!-- ❌ SECCIÓN ELIMINADA: Opciones de personalización -->
                        <!-- Ya no se muestran porque se gestionan en su propio menú -->
                    </div>

                    <!-- Botones -->
                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.products.index') }}" class="btn-retro-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-retro-primary">
                            <i class="bi bi-save"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
