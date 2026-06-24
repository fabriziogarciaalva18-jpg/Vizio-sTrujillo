@extends('layouts.retro')

@section('title', 'Crear Categoría')

@section('content')
<div class="container py-4">
    <h1 class="section-title" style="font-size: 1.5rem;">← CREAR CATEGORÍA →</h1>
    <div class="section-divider"></div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="profile-card">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nombre de la categoría *</label>
                        <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name') }}" required placeholder="Ej: Tortas, Pasteles...">
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" rows="3" class="form-control form-control-retro" placeholder="Describe brevemente esta categoría...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Posición de visualización</label>
                        <input type="number" name="sort_order" class="form-control form-control-retro" value="{{ old('sort_order', 0) }}" placeholder="0 = primero">
                        <small class="text-muted">Define el orden en que aparecerán las categorías en el catálogo.</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Activa (visible en el catálogo)</label>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.categories.index') }}" class="btn-retro-secondary">Cancelar</a>
                        <button type="submit" class="btn-retro-primary">Guardar categoría</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
