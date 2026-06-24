@extends('layouts.retro')

@section('title', 'Gestionar Categorías')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title" style="font-size: 1.5rem;">← CATEGORÍAS →</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn-retro-primary">
            <i class="bi bi-plus-circle"></i> NUEVA CATEGORÍA
        </a>
    </div>
    <div class="section-divider"></div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #DCFCE7; color: #166534;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #FEE2E2; color: #991B1B;">{{ session('error') }}</div>
    @endif

    <div class="admin-card-retro">
        <div class="table-responsive">
            <table class="admin-table-retro">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>{{ Str::limit($cat->description, 40) }}</td>
                        <td>{{ $cat->sort_order }}</td>
                        <td>
                            <form action="{{ route('admin.categories.toggle-status', $cat) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="status-badge" style="border:none; cursor:pointer; background: {{ $cat->is_active ? '#DCFCE7' : '#FEE2E2' }}; color: {{ $cat->is_active ? '#166534' : '#991B1B' }};">
                                    {{ $cat->is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="btn-retro-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-retro-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No hay categorías registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
