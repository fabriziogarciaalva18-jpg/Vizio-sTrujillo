@extends('layouts.retro')

@section('title', 'Dashboard - Admin Vizio\'s')

@section('content')
<div class="container-fluid px-4">
    <!-- ========================================== -->
    <!-- HEADER                                      -->
    <!-- ========================================== -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 style="font-family: 'DM Serif Display', serif; font-size: 1.75rem; letter-spacing: -0.5px; margin: 0;">
                Dashboard
            </h1>
            <p class="text-muted" style="font-size: 0.8rem; margin-top: 0.25rem;">
                <i class="bi bi-person-circle"></i> Bienvenido de vuelta, {{ auth()->user()->name }}
            </p>
        </div>
        <div class="text-end">
            <div class="date-display" style="background: var(--gray-50); padding: 0.5rem 1rem; border-radius: 30px;">
                <i class="bi bi-calendar3"></i>
                {{ now()->setTimezone('America/Lima')->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- FILA 1: ESTADÍSTICAS PRINCIPALES            -->
    <!-- ========================================== -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro bg-primary">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>{{ number_format($stats['total_orders']) }}</h3>
                    <p>Total Pedidos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro bg-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>S/. {{ number_format($stats['total_revenue'] / 100, 0) }}</h3>
                    <p>Ingresos Totales</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro bg-info">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>{{ number_format($stats['total_products']) }}</h3>
                    <p>Productos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro bg-warning">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>{{ number_format($stats['total_users']) }}</h3>
                    <p>Usuarios</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- FILA 2: ESTADÍSTICAS SECUNDARIAS           -->
    <!-- ========================================== -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro bg-danger">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>{{ number_format($stats['pending_orders']) }}</h3>
                    <p>Pedidos Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro" style="background: #6E6860;">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>{{ number_format($stats['delivering_orders']) }}</h3>
                    <p>En Camino</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro" style="background: #9E9890;">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>{{ number_format($stats['pending_review_payments']) }}</h3>
                    <p>Pagos por Revisar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-retro">
                <div class="stat-icon-retro" style="background: #28a745;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-info-retro">
                    <h3>{{ number_format($stats['delivered_orders']) }}</h3>
                    <p>Entregados</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- FILA 3: ACCESO RÁPIDO A CATEGORÍAS Y PERSONALIZACIONES -->
    <!-- ========================================== -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
                <div class="stat-card-retro text-center p-4" style="cursor: pointer; transition: all 0.2s;">
                    <i class="bi bi-tags" style="font-size: 2.5rem; color: #166534;"></i>
                    <h4 class="mt-2">Categorías</h4>
                    <p class="text-muted small">Gestiona las categorías de productos</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('admin.customizations.index') }}" class="text-decoration-none">
                <div class="stat-card-retro text-center p-4" style="cursor: pointer; transition: all 0.2s;">
                    <i class="bi bi-palette" style="font-size: 2.5rem; color: #92400E;"></i>
                    <h4 class="mt-2">Personalizaciones</h4>
                    <p class="text-muted small">Administra tamaños, sabores, rellenos...</p>
                </div>
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- FILA 4: PEDIDOS RECIENTES Y PRODUCTOS INACTIVOS -->
    <!-- ========================================== -->
    <div class="row g-4">
        <!-- Columna izquierda: Pedidos recientes -->
        <div class="col-lg-8">
            <div class="admin-card-retro">
                <div class="card-header-retro">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt" style="color: var(--gray-400);"></i>
                        <h5 class="mb-0" style="font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase;">Pedidos Recientes</h5>
                    </div>
                    <a href="{{ route('admin.orders') }}" class="filter-btn" style="font-size: 0.65rem; padding: 0.3rem 0.8rem;">
                        <i class="bi bi-arrow-right"></i> Ver todos
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table-retro">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_orders'] as $order)
                            <tr>
                                <td class="fw-semibold">#{{ substr($order->order_number, -8) }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>S/. {{ number_format($order->total, 2) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($order->status) {
                                            'pending' => 'status-pending',
                                            'confirmed' => 'status-confirmed',
                                            'preparing' => 'status-preparing',
                                            'ready' => 'status-ready',
                                            'delivering' => 'status-delivering',
                                            'delivered' => 'status-delivered',
                                            'cancelled' => 'status-cancelled',
                                            'rejected' => 'status-rejected',
                                            default => 'status-pending'
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        <i class="bi bi-circle-fill" style="font-size: 0.35rem;"></i>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="status-badge" style="background: #DCFCE7; color: #166534;">
                                            <i class="bi bi-check-circle-fill"></i> Pagado
                                        </span>
                                    @elseif($order->payment_status == 'pending_review')
                                        <span class="status-badge" style="background: #FEF3C7; color: #92400E;">
                                            <i class="bi bi-clock-history"></i> En revisión
                                        </span>
                                    @elseif($order->payment_status == 'rejected')
                                        <span class="status-badge" style="background: #FEE2E2; color: #991B1B;">
                                            <i class="bi bi-x-circle-fill"></i> Rechazado
                                        </span>
                                    @else
                                        <span class="status-badge" style="background: var(--gray-100); color: var(--gray-500);">
                                            <i class="bi bi-hourglass-split"></i> Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td style="font-family: monospace; font-size: 0.7rem;">{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna derecha: Productos inactivos -->
        <div class="col-lg-4">
            <div class="admin-card-retro">
                <div class="card-header-retro">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam" style="color: var(--gray-400);"></i>
                        <h5 class="mb-0" style="font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase;">Productos Inactivos</h5>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="filter-btn" style="font-size: 0.65rem; padding: 0.3rem 0.8rem;">
                        <i class="bi bi-gear"></i> Gestionar
                    </a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($stats['inactive_products'] as $product)
                    <div class="list-group-item-retro d-flex justify-content-between align-items-center">
                        <div>
                            <strong style="font-size: 0.85rem;">{{ $product->name }}</strong>
                            <small class="text-muted d-block" style="font-size: 0.65rem;">
                                <i class="bi bi-tag"></i> {{ $product->category->name ?? 'Sin categoría' }}
                            </small>
                        </div>
                        <span class="status-badge" style="background: var(--gray-100); color: var(--gray-500);">
                            <i class="bi bi-ban"></i> Inactivo
                        </span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                        <p class="mt-2" style="font-size: 0.8rem;">Todos los productos están activos</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
