@extends('layouts.retro')

@section('title', 'Admin - Pagos Pendientes')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title" style="font-size: 1.5rem;">← PAGOS POR REVISAR →</h1>
            <div class="section-divider"></div>
        </div>
    </div>

    <div class="admin-card-retro">
        <div class="table-responsive">
            <table class="admin-table-retro">
                <thead>
                    <tr>
                        <th>Pedido #</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Referencia</th>
                        <th>Comprobante</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments as $order)
                    <tr>
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                        <td>S/. {{ number_format($order->total, 2) }}</td>
                        <td>{{ strtoupper($order->payment_method) }}</td>
                        <td>{{ $order->payment_reference ?? '-' }}</td>
                        <td>
                            @if($order->voucher_path)
                                <a href="{{ asset('storage/' . $order->voucher_path) }}" target="_blank" class="btn-retro-secondary btn-sm">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <form action="{{ route('payment.mark-paid', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-retro-primary btn-sm" onclick="return confirm('¿Confirmar este pago?')">
                                        <i class="bi bi-check-circle"></i> Confirmar
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn-retro-danger btn-sm" onclick="return confirm('¿Rechazar este pago?')">
                                        <i class="bi bi-x-circle"></i> Rechazar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                            <p class="mt-2">No hay pagos pendientes de revisión.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
