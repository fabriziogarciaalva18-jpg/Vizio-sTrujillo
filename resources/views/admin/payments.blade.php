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
                    @foreach($pendingPayments as $order)
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
                            <form action="{{ route('payment.mark-paid', $order) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-retro-primary btn-sm" onclick="return confirm('¿Confirmar este pago?')">
                                    <i class="bi bi-check-circle"></i> Confirmar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
