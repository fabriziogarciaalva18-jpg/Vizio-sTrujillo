@extends('layouts.retro')

@section('title', 'Gestionar Personalizaciones')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title" style="font-size: 1.5rem;">← PERSONALIZACIONES →</h1>
        <a href="{{ route('admin.customizations.create') }}" class="btn-retro-primary">
            <i class="bi bi-plus-circle"></i> NUEVA PERSONALIZACIÓN
        </a>
    </div>
    <div class="section-divider"></div>

    <!-- Filtros -->
    <div class="admin-card-retro p-3 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Producto</label>
                <select name="product_id" class="form-select form-control-retro" onchange="this.form.submit()">
                    <option value="">Todos los productos</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Tipo de personalización</label>
                <select name="config_type" class="form-select form-control-retro" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('config_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <a href="{{ route('admin.customizations.index') }}" class="btn-retro-secondary d-block text-center">Limpiar filtros</a>
            </div>
        </form>
    </div>

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
                        <th>ID</th>
                        <th>Productos aplicables</th>
                        <th>Tipo</th>
                        <th>Opción</th>
                        <th>Precio extra</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($configurations as $config)
                    <tr>
                        <td>{{ $config->id }}</td>
                        <td>
                            @if($config->products->count() > 0)
                                @foreach($config->products as $product)
                                    <span class="badge bg-light text-dark">{{ $product->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Sin productos</span>
                            @endif
                        </td>
                        <td>{{ $types[$config->config_type] ?? ucfirst($config->config_type) }}</td>
                        <td><strong>{{ $config->name }}</strong></td>
                        <td>S/. {{ number_format($config->price_modifier, 2) }}</td>
                        <td>{{ $config->sort_order }}</td>
                        <td>
                            <form action="{{ route('admin.customizations.toggle-status', $config) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="status-badge" style="border:none; cursor:pointer; background: {{ $config->is_active ? '#DCFCE7' : '#FEE2E2' }}; color: {{ $config->is_active ? '#166534' : '#991B1B' }};">
                                    {{ $config->is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.customizations.edit', $config) }}" class="btn-retro-secondary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.customizations.destroy', $config) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta personalización?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-retro-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-emoji-frown" style="font-size: 2rem;"></i>
                            <p class="mt-2">No hay personalizaciones registradas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
