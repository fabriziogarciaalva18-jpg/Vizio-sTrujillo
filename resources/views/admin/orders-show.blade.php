@extends('layouts.retro')

@section('title', 'Detalle del Pedido #' . $order->order_number)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="section-title" style="font-size: 1.5rem;">← DETALLE DEL PEDIDO →</h1>
            <div class="section-divider"></div>
        </div>
        <a href="{{ route('admin.orders') }}" class="btn-retro-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver a pedidos
        </a>
    </div>

    <div class="row">
        <!-- ========================================== -->
        <!-- COLUMNA IZQUIERDA: INFORMACIÓN DEL PEDIDO -->
        <!-- ========================================== -->
        <div class="col-lg-8">
            <div class="profile-card">
                <h3 class="profile-section-title">INFORMACIÓN DEL PEDIDO</h3>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Número de pedido:</strong> #{{ $order->order_number }}</p>
                        <p><strong>Fecha de creación:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                        <p><strong>Cliente:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                        <p><strong>Teléfono:</strong> {{ $order->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado del pedido:</strong>
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
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                        <p><strong>Estado del pago:</strong>
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
                        </p>
                        <p><strong>Método de pago:</strong> {{ strtoupper($order->payment_method) }}</p>
                        @if($order->payment_reference)
                            <p><strong>Referencia de pago:</strong> {{ $order->payment_reference }}</p>
                        @endif
                        <p><strong>Tipo de entrega:</strong>
                            @if($order->delivery_type == 'pickup')
                                <span class="badge bg-info text-dark"><i class="bi bi-shop"></i> Recojo en tienda</span>
                            @else
                                <span class="badge bg-primary"><i class="bi bi-truck"></i> Envío a domicilio</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="mt-3">
                    <h5><i class="bi bi-geo-alt"></i> Ubicación de entrega</h5>
                    <p><strong>Dirección:</strong> {{ $order->delivery_address }}</p>
                    @if($order->district)
                        <p><strong>Distrito:</strong> {{ $order->district }}</p>
                    @endif
                    @if($order->reference_point)
                        <p><strong>Punto de referencia:</strong> {{ $order->reference_point }}</p>
                    @endif
                    @if($order->delivery_reference)
                        <p><strong>Referencia de entrega:</strong> {{ $order->delivery_reference }}</p>
                    @endif
                    @if($order->special_instructions)
                        <p><strong>Instrucciones especiales:</strong> {{ $order->special_instructions }}</p>
                    @endif
                    @if($order->delivery_distance)
                        <p><strong>Distancia:</strong> {{ number_format($order->delivery_distance, 2) }} km</p>
                    @endif
                </div>

                <hr>

                <div class="mt-3">
                    <h5><i class="bi bi-box-seam"></i> Productos</h5>
                    <div class="table-responsive">
                        <table class="admin-table-retro">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Personalización</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>
                                        @php
                                            $config = is_string($item->configuration) ? json_decode($item->configuration, true) : $item->configuration;
                                        @endphp
                                        @if($config && is_array($config))
                                            <ul class="list-unstyled mb-0 small">
                                                @if(!empty($config['message']))
                                                    <li><i class="bi bi-chat-text"></i> "{{ $config['message'] }}"</li>
                                                @endif
                                                @if(!empty($config['selected_configs']))
                                                    @foreach($config['selected_configs'] as $type => $configId)
                                                        @php
                                                            $configModel = App\Models\ProductConfiguration::find($configId);
                                                            $label = match($type) {
                                                                'size' => 'Tamaño',
                                                                'layers' => 'Pisos',
                                                                'flavor' => 'Sabor',
                                                                'filling' => 'Relleno',
                                                                'covering' => 'Cobertura',
                                                                'shape' => 'Forma',
                                                                'color' => 'Color',
                                                                'toppings' => 'Toppings',
                                                                'decoration' => 'Decoración',
                                                                default => ucfirst($type)
                                                            };
                                                        @endphp
                                                        <li><span class="text-muted">{{ $label }}:</span> {{ $configModel->name ?? $configId }}</li>
                                                    @endforeach
                                                @endif
                                                @if(!empty($config['selected_addons']))
                                                    <li><span class="text-muted">Adicionales:</span>
                                                        @foreach($config['selected_addons'] as $addonId)
                                                            @php $addon = App\Models\Addon::find($addonId); @endphp
                                                            {{ $addon->name ?? $addonId }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </li>
                                                @endif
                                            </ul>
                                        @else
                                            <span class="text-muted">Sin personalización</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>S/. {{ number_format($item->unit_price, 2) }}</td>
                                    <td>S/. {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Subtotal:</th>
                                    <td>S/. {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Envío:</th>
                                    <td>S/. {{ number_format($order->delivery_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <td><strong>S/. {{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Comprobante de pago -->
                @if($order->voucher_path)
                <div class="mt-4">
                    <h5><i class="bi bi-file-image"></i> Comprobante de pago</h5>
                    <div class="alert alert-retro" style="background: #E0F2FE; color: #075985;">
                        <i class="bi bi-info-circle"></i> El cliente ha subido un comprobante de pago.
                        @if($order->payment_reference)
                            <br><strong>Referencia:</strong> {{ $order->payment_reference }}
                        @endif
                    </div>
                    <div class="text-center">
                        <a href="{{ asset('storage/' . $order->voucher_path) }}" target="_blank" class="btn-retro-primary">
                            <i class="bi bi-eye"></i> Ver comprobante completo
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- COLUMNA DERECHA: ACCIONES Y PAGO -->
        <!-- ========================================== -->
        <div class="col-lg-4">
            <!-- Panel de acciones -->
            <div class="profile-card mb-4">
                <h3 class="profile-section-title">ACCIONES</h3>

                @if($order->status == 'cancelled')
                    <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Este pedido fue cancelado por el usuario y no puede ser modificado.
                    </div>
                @elseif($order->status == 'rejected')
                    <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Este pedido fue rechazado y no puede ser modificado.
                    </div>
                @else
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Cambiar estado</label>
                            <select name="status" class="form-select form-control-retro">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparando</option>
                                <option value="delivering" {{ $order->status == 'delivering' ? 'selected' : '' }}>En camino</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                <option value="rejected" {{ $order->status == 'rejected' ? 'selected' : '' }}>Rechazar</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-retro-primary w-100">Actualizar estado</button>
                    </form>
                @endif
            </div>

            <!-- Panel de pago (solo si está en revisión) -->
            @if($order->payment_status == 'pending_review' && $order->voucher_path)
            <div class="profile-card">
                <h3 class="profile-section-title">
                    <i class="bi bi-credit-card"></i> REVISAR PAGO
                </h3>
                <p class="text-muted small">El cliente ha subido un comprobante. Revisa la imagen y confirma o rechaza el pago.</p>

                <div class="d-grid gap-2">
                    <form action="{{ route('payment.mark-paid', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-retro-primary w-100" onclick="return confirm('¿Confirmar este pago?')">
                            <i class="bi bi-check-circle"></i> Aprobar pago
                        </button>
                    </form>

                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn-retro-danger w-100" onclick="return confirm('¿Rechazar este pago?')">
                            <i class="bi bi-x-circle"></i> Rechazar pago
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
