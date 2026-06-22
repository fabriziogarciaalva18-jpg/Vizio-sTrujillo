@extends('layouts.retro')

@section('title', 'Admin - Gestionar Productos')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title" style="font-size: 1.5rem;">← GESTIONAR PRODUCTOS →</h1>
            <div class="section-divider"></div>
        </div>
        <button class="btn-retro-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle"></i> NUEVO PRODUCTO
        </button>
    </div>

    <div class="admin-card-retro">
        <div class="table-responsive">
            <table class="admin-table-retro">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                        <td>S/. {{ number_format($product->base_price / 100, 2) }}</td>
                        <td>{{ $product->stock ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge" style="background: {{ $product->is_active ? '#DCFCE7' : '#FEE2E2' }}; color: {{ $product->is_active ? '#166534' : '#991B1B' }};">
                                {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn-retro-secondary btn-sm" onclick="editProduct({{ $product->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn-retro-danger btn-sm" onclick="deleteProduct({{ $product->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection