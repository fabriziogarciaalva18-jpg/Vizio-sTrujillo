@extends('layouts.retro')

@section('title', 'Detalle de Entrega - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="section-title" style="font-size: 1.5rem;">← DETALLE DE ENTREGA →</h1>
                <div class="section-divider"></div>
            </div>
            <a href="{{ route('delivery.orders') }}" class="btn-retro-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver a mis entregas
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="profile-card">
                <h3 class="profile-section-title"><i class="bi bi-box-seam"></i> INFORMACIÓN DEL PEDIDO</h3>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-hash"></i> Pedido:</strong> #{{ $order->order_number }}</p>
                        <p><strong><i class="bi bi-person"></i> Cliente:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-phone"></i> Teléfono:</strong> {{ $order->phone }}</p>
                        <p><strong><i class="bi bi-calendar"></i> Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-truck"></i> Estado:</strong>
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'status-pending',
                                    'confirmed' => 'status-confirmed',
                                    'preparing' => 'status-preparing',
                                    'delivering' => 'status-delivering',
                                    'delivered' => 'status-delivered',
                                    'cancelled' => 'status-cancelled',
                                    default => 'status-pending'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                        </p>
                        <p><strong><i class="bi bi-credit-card"></i> Método de pago:</strong> {{ strtoupper($order->payment_method) }}</p>
                        <p><strong><i class="bi bi-currency-dollar"></i> Total:</strong> S/. {{ number_format($order->total, 2) }}</p>
                    </div>
                </div>

                <hr>

                <div class="mt-3">
                    <h5><i class="bi bi-geo-alt"></i> Ubicación de entrega</h5>
                    <p><strong>Dirección:</strong> {{ $order->delivery_address }}</p>
                    <p><strong>Distrito:</strong> {{ $order->district ?? 'No especificado' }}</p>

                    @if($order->delivery_reference)
                    <div class="alert alert-retro mt-2" style="background: #E0F2FE; color: #075985; border: 1px solid #7DD3FC;">
                        <i class="bi bi-pin"></i> <strong>Referencia de entrega:</strong>
                        <p class="mb-0">{{ $order->delivery_reference }}</p>
                    </div>
                    @endif

                    @if($order->address_lat && $order->address_lng)
                    <div class="mt-2">
                        <a href="https://www.google.com/maps/dir/{{ config('delivery.store.lat') }},{{ config('delivery.store.lng') }}/{{ $order->address_lat }},{{ $order->address_lng }}"
                           target="_blank" class="btn-retro-primary btn-sm">
                            <i class="bi bi-map"></i> Ver ruta en Google Maps
                        </a>
                        @if($order->delivery_distance)
                            <span class="ms-2 text-muted"><i class="bi bi-rulers"></i> {{ number_format($order->delivery_distance, 1) }} km</span>
                        @endif
                    </div>
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
                                                        @if($configModel)
                                                        <li><span class="text-muted">{{ $label }}:</span> {{ $configModel->name }}</li>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if(!empty($config['message']))
                                                    <li><span class="text-muted">Mensaje:</span> "{{ $config['message'] }}"</li>
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

                @if($order->special_instructions)
                <div class="mt-3">
                    <h5><i class="bi bi-chat-text"></i> Instrucciones especiales</h5>
                    <p class="text-muted">{{ $order->special_instructions }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="profile-card">
                <h3 class="profile-section-title"><i class="bi bi-gear"></i> ACCIONES</h3>

                @if($order->status == 'delivering')
                    <div class="d-grid gap-2">
                        <form action="{{ route('delivery.orders.confirm', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-retro-primary w-100" onclick="return confirm('¿Confirmar que has entregado este pedido?')">
                                <i class="bi bi-check-circle"></i> CONFIRMAR ENTREGA
                            </button>
                        </form>
                        <form action="{{ route('delivery.orders.failed', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-retro-danger w-100" onclick="return confirm('¿Marcar esta entrega como fallida?')">
                                <i class="bi bi-x-circle"></i> MARCAR COMO FALLIDA
                            </button>
                        </form>
                    </div>
                @elseif($order->status == 'delivered')
                    <div class="alert alert-retro text-center" style="background: #DCFCE7; color: #166534;">
                        <i class="bi bi-check-circle-fill"></i> Pedido entregado
                    </div>
                @elseif($order->status == 'cancelled' || $order->status == 'rejected')
                    <div class="alert alert-retro text-center" style="background: #FEE2E2; color: #991B1B;">
                        <i class="bi bi-x-circle-fill"></i> Pedido {{ $order->status }}
                    </div>
                @else
                    <div class="alert alert-retro text-center" style="background: #FEF3C7; color: #92400E;">
                        <i class="bi bi-clock-history"></i> Pedido en preparación
                    </div>
                @endif
            </div>

            <div class="profile-card mt-3">
                <h3 class="profile-section-title"><i class="bi bi-info-circle"></i> DATOS RÁPIDOS</h3>
                <ul class="list-unstyled">
                    <li><i class="bi bi-person"></i> {{ $order->user->name ?? 'N/A' }}</li>
                    <li><i class="bi bi-phone"></i> {{ $order->phone }}</li>
                    <li><i class="bi bi-geo-alt"></i> {{ $order->district ?? 'No especificado' }}</li>
                    @if($order->delivery_reference)
                        <li><i class="bi bi-pin"></i> {{ Str::limit($order->delivery_reference, 30) }}</li>
                    @endif
                </ul>
                <a href="tel:{{ $order->phone }}" class="btn-retro-primary w-100">
                    <i class="bi bi-telephone"></i> LLAMAR AL CLIENTE
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
