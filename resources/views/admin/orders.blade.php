@extends('layouts.retro')

@section('title', 'Admin - Gestionar Pedidos')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title" style="font-size: 1.5rem;">← GESTIONAR PEDIDOS →</h1>
            <div class="section-divider"></div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="admin-card-retro mb-4 p-3">
        <form action="{{ route('admin.orders') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-search"></i> Buscar</label>
                <input type="text" name="search" class="form-control form-control-retro"
                       placeholder="#Pedido o Cliente" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="bi bi-funnel"></i> Estado</label>
                <select name="status" class="form-select form-control-retro">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparando</option>
                    <option value="delivering" {{ request('status') == 'delivering' ? 'selected' : '' }}>En camino</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregado</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazado</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><i class="bi bi-calendar"></i> Desde</label>
                <input type="date" name="date_from" class="form-control form-control-retro" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label"><i class="bi bi-calendar"></i> Hasta</label>
                <input type="date" name="date_to" class="form-control form-control-retro" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-retro-primary w-100"><i class="bi bi-filter"></i> Filtrar</button>
                    <a href="{{ route('admin.orders') }}" class="btn-retro-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </div>
        </form>
    </div>

    <!-- STATS CARDS (globales) -->
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

    <!-- TABLA DE PEDIDOS -->
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
                        <th>Comprobante</th>
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
                        <td>S/. {{ number_format($order->total, 2) }}</td>

                        <!-- ESTADO (consistente con dashboard) -->
                        <td>
                            @if(in_array($order->status, ['cancelled', 'rejected']))
                                <span class="status-badge status-{{ $order->status == 'cancelled' ? 'cancelled' : 'rejected' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                @if($order->status == 'cancelled')
                                @endif
                            @else
                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline-block w-100">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group input-group-sm">
                                        <select name="status" class="form-select form-select-sm" style="font-size: 0.7rem; width: auto;" onchange="this.form.submit()">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                            <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparando</option>
                                            <option value="delivering" {{ $order->status == 'delivering' ? 'selected' : '' }}>En camino</option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                            <option value="rejected" {{ $order->status == 'rejected' ? 'selected' : '' }}>Rechazar</option>
                                        </select>
                                    </div>
                                </form>
                            @endif
                        </td>

                        <!-- PAGO -->
                        <td>
                            @php
                                $paymentBadgeClass = match($order->payment_status) {
                                    'paid' => 'bg-success',
                                    'pending_review' => 'bg-warning text-dark',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $paymentBadgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                        </td>

                        <!-- COMPROBANTE -->
                        <td>
                            @if($order->voucher_path)
                                <a href="{{ asset('storage/' . $order->voucher_path) }}" target="_blank" class="btn-retro-secondary btn-sm">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>

                        <!-- ACCIONES -->
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-retro-secondary btn-sm" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2">No hay pedidos con los filtros seleccionados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 d-flex justify-content-center">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
