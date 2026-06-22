@extends('layouts.retro')

@section('title', 'Editar Producto')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← EDITAR PRODUCTO →</h1>
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
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Categoría *</label>
                            <select name="category_id" class="form-select form-control-retro" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción *</label>
                            <textarea name="description" rows="4" class="form-control form-control-retro" required>{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Precio (S/.) *</label>
                            <input type="number" step="0.01" name="base_price" class="form-control form-control-retro" value="{{ old('base_price', $product->base_price) }}" required placeholder="Ej: 350.00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo de producto</label>
                            <select name="product_type" class="form-select form-control-retro">
                                <option value="simple" {{ $product->product_type == 'simple' ? 'selected' : '' }}>Simple</option>
                                <option value="configurable" {{ $product->product_type == 'configurable' ? 'selected' : '' }}>Configurable</option>
                                <option value="catering" {{ $product->product_type == 'catering' ? 'selected' : '' }}>Catering</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Imagen actual</label>
                            @if($product->image_url && !filter_var($product->image_url, FILTER_VALIDATE_URL))
                                <div class="mb-2">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="80" style="border-radius: 8px;">
                                </div>
                            @endif
                            <input type="file" name="image_url" class="form-control form-control-retro" accept="image/*">
                            <small>Dejar vacío para mantener la imagen actual</small>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $product->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activo</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr>
                            <h5>Personalización</h5>
                            <div class="form-check">
                                <input type="checkbox" name="has_sizes" id="has_sizes" {{ $product->has_sizes ? 'checked' : '' }}>
                                <label for="has_sizes">Tamaños</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_layers" id="has_layers" {{ $product->has_layers ? 'checked' : '' }}>
                                <label for="has_layers">Pisos</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_flavors" id="has_flavors" {{ $product->has_flavors ? 'checked' : '' }}>
                                <label for="has_flavors">Sabores</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_fillings" id="has_fillings" {{ $product->has_fillings ? 'checked' : '' }}>
                                <label for="has_fillings">Rellenos</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="has_coverings" id="has_coverings" {{ $product->has_coverings ? 'checked' : '' }}>
                                <label for="has_coverings">Coberturas</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.products.index') }}" class="btn-retro-secondary">Cancelar</a>
                        <button type="submit" class="btn-retro-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
