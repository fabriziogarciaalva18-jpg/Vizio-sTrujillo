@extends('layouts.retro')

@section('title', 'Admin - Gestionar Pedidos')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title" style="font-size: 1.5rem;">← GESTIONAR PEDIDOS →</h1>
            <div class="section-divider"></div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $statusCounts['pending'] ?? 0 }}</h3>
                <small class="text-muted">Pendientes</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $statusCounts['confirmed'] ?? 0 }}</h3>
                <small class="text-muted">Confirmados</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $statusCounts['preparing'] ?? 0 }}</h3>
                <small class="text-muted">Preparando</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $statusCounts['delivering'] ?? 0 }}</h3>
                <small class="text-muted">En camino</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $statusCounts['delivered'] ?? 0 }}</h3>
                <small class="text-muted">Entregados</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card-retro text-center p-3">
                <h3 class="mb-0">{{ $statusCounts['cancelled'] ?? 0 }}</h3>
                <small class="text-muted">Cancelados</small>
            </div>
        </div>
    </div>

    <!-- Tabla de pedidos -->
    <div class="admin-card-retro">
        <div class="table-responsive">
            <table class="admin-table-retro">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pedido #</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Pago</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                        <td>S/. {{ number_format($order->total / 100, 2) }}</td>
                        <td>
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block; font-size: 0.7rem;" onchange="this.form.submit()">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                    <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparando</option>
                                    <option value="delivering" {{ $order->status == 'delivering' ? 'selected' : '' }}>En camino</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            @if($order->payment_status == 'paid')
                                <span class="badge bg-success">Pagado</span>
                            @elseif($order->payment_status == 'pending_review')
                                <span class="badge bg-warning text-dark">En revisión</span>
                            @else
                                <span class="badge bg-secondary">Pendiente</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn-retro-secondary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2">No hay pedidos registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection