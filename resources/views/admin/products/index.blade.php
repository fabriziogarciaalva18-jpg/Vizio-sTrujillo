@extends('layouts.retro')

@section('title', 'Administrar Productos')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title" style="font-size: 1.8rem;">← GESTIÓN DE PRODUCTOS →</h1>
            <div class="section-divider"></div>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-retro-primary">
            <i class="bi bi-plus-circle"></i> NUEVO PRODUCTO
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #DCFCE7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-card-retro">
        <div class="table-responsive">
            <table class="admin-table-retro">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio (S/.)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="50" height="50" style="object-fit: cover; border-radius: 8px;">
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                        <td>S/. {{ number_format($product->base_price, 2) }}</td>
                        <td>
                            <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="status-badge" style="border:none; cursor:pointer; background: {{ $product->is_active ? '#DCFCE7' : '#FEE2E2' }}; color: {{ $product->is_active ? '#166534' : '#991B1B' }}; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem;">
                                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-retro-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este producto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-retro-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No hay productos registrados. <a href="{{ route('admin.products.create') }}">Crear uno</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection
