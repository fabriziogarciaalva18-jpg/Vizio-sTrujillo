@extends('layouts.retro')

@section('title', 'Detalle de entrega #' . $order->order_number)

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="section-title" style="font-size: 1.5rem;">← DETALLE DE ENTREGA →</h1>
            <a href="{{ route('delivery.dashboard') }}" class="btn-retro-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
        <div class="section-divider"></div>
    </div>

    <div class="row g-4">
        <!-- Información del pedido -->
        <div class="col-lg-7">
            <div class="profile-card">
                <h3 class="profile-section-title"><i class="bi bi-receipt"></i> INFORMACIÓN DEL PEDIDO</h3>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-hash"></i> Número:</strong> #{{ $order->order_number }}</p>
                        <p><strong><i class="bi bi-person"></i> Cliente:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-phone"></i> Teléfono:</strong> {{ $order->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-currency-dollar"></i> Total:</strong> S/. {{ number_format($order->total, 2) }}</p>
                        <p><strong><i class="bi bi-calendar"></i> Entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                        <p><strong><i class="bi bi-truck"></i> Estado:</strong>
                            <span class="status-badge status-preparing">EN PREPARACIÓN</span>
                        </p>
                    </div>
                </div>

                <hr>

                <h5><i class="bi bi-geo-alt"></i> DIRECCIÓN DE ENTREGA</h5>
                <p><strong>Dirección:</strong> {{ $order->delivery_address }}</p>
                <p><strong>Distrito:</strong> {{ $order->district }}</p>
                @if($order->reference_point)
                    <p><strong><i class="bi bi-pin"></i> Referencia:</strong> {{ $order->reference_point }}</p>
                @endif
                @if($order->special_instructions)
                    <p><strong><i class="bi bi-chat-text"></i> Instrucciones:</strong> {{ $order->special_instructions }}</p>
                @endif

                <hr>

                <h5><i class="bi bi-box-seam"></i> PRODUCTOS</h5>
                <div class="table-responsive">
                    <table class="admin-table-retro">
                        <thead>
                            <tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>S/. {{ number_format($item->unit_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mapa de ubicación -->
                @if($order->latitude && $order->longitude)
                <hr>
                <h5><i class="bi bi-map"></i> UBICACIÓN</h5>
                <div class="mb-3" style="height: 300px; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ $order->longitude - 0.01 }}%2C{{ $order->latitude - 0.01 }}%2C{{ $order->longitude + 0.01 }}%2C{{ $order->latitude + 0.01 }}&amp;layer=mapnik&amp;marker={{ $order->latitude }}%2C{{ $order->longitude }}"
                        style="width: 100%; height: 100%; border: 0; border-radius: 8px;">
                    </iframe>
                </div>
                <a href="https://www.google.com/maps/dir/{{ $storeLocation['lat'] }},{{ $storeLocation['lng'] }}/{{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="btn-retro-primary">
                    <i class="bi bi-map"></i> Cómo llegar
                </a>
                @endif
            </div>
        </div>

        <!-- Acciones de entrega -->
        <div class="col-lg-5">
            <div class="profile-card">
                <h3 class="profile-section-title"><i class="bi bi-truck"></i> ACCIONES</h3>

                <p class="text-muted small">
                    <i class="bi bi-info-circle"></i> Confirma la entrega solo después de haber entregado el pedido.
                </p>

                <form action="{{ route('delivery.orders.confirm', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-chat-text"></i> Nota de entrega</label>
                        <textarea name="delivery_note" class="form-control form-control-retro" rows="2" placeholder="Entregado a: ..."></textarea>
                    </div>
                    <button type="submit" class="btn-retro-primary w-100 py-2" onclick="return confirm('¿Confirmar que el pedido ha sido entregado?')">
                        <i class="bi bi-check-circle"></i> CONFIRMAR ENTREGA
                    </button>
                </form>

                <hr>

                <form action="{{ route('delivery.orders.failed', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-exclamation-triangle"></i> Motivo de fallo</label>
                        <select name="failure_reason" class="form-select form-control-retro" required>
                            <option value="">Selecciona un motivo...</option>
                            <option value="Cliente ausente">Cliente ausente</option>
                            <option value="Dirección incorrecta">Dirección incorrecta</option>
                            <option value="No contesta">No contesta al teléfono</option>
                            <option value="Cliente canceló">Cliente canceló</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-retro-danger w-100" onclick="return confirm('¿Marcar esta entrega como fallida? El pedido volverá a la cola.')">
                        <i class="bi bi-x-circle"></i> MARCAR COMO FALLIDA
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
