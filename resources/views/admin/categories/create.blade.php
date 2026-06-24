@extends('layouts.retro')

@section('title', 'Crear Categoría')

@section('content')
<div class="container py-4">
    <h1 class="section-title" style="font-size: 1.5rem;">← CREAR CATEGORÍA →</h1>
    <div class="section-divider"></div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="profile-card">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="name" class="form-control form-control-retro" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" rows="3" class="form-control form-control-retro">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icono (clase Bootstrap Icons)</label>
                        <input type="text" name="icon" class="form-control form-control-retro" value="{{ old('icon') }}" placeholder="Ej: bi-cake">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Orden</label>
                        <input type="number" name="sort_order" class="form-control form-control-retro" value="{{ old('sort_order', 0) }}">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.categories.index') }}" class="btn-retro-secondary">Cancelar</a>
                        <button type="submit" class="btn-retro-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
