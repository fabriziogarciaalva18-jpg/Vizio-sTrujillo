@extends('layouts.retro')

@section('title', 'Pedido #' . $order->order_number)

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← DETALLE DEL PEDIDO →</h1>
        <div class="section-divider"></div>
    </div>

    @if(session('success'))
        <div class="alert alert-retro" style="background: #DCFCE7; color: #166534;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B;">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- ========================================== -->
        <!-- COLUMNA IZQUIERDA: Información del pedido  -->
        <!-- ========================================== -->
        <div class="col-lg-8">
            <div class="profile-card">
                <h3 class="profile-section-title">PEDIDO #{{ $order->order_number }}</h3>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Estado:</strong>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </p>
                        <p><strong>Fecha de creación:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Método de pago:</strong> {{ strtoupper($order->payment_method) }}</p>
                        <p><strong>Estado de pago:</strong>
                            @if($order->payment_status == 'paid')
                                <span class="status-badge" style="background: #DCFCE7; color: #166534;">Pagado</span>
                            @elseif($order->payment_status == 'pending_review')
                                <span class="status-badge" style="background: #FEF3C7; color: #92400E;">En revisión</span>
                            @elseif($order->payment_status == 'pending_delivery')
                                <span class="status-badge" style="background: #E0F2FE; color: #075985;">Pendiente (contra entrega)</span>
                            @else
                                <span class="status-badge" style="background: var(--gray-100); color: var(--gray-500);">Pendiente</span>
                            @endif
                        </p>
                        @if($order->payment_reference)
                            <p><strong>Referencia de pago:</strong> {{ $order->payment_reference }}</p>
                        @endif
                    </div>
                </div>

                <hr>

                <h4>Productos</h4>
                <div class="table-responsive">
                    <table class="admin-table">
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
                    </table>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-geo-alt"></i> Dirección:</strong> {{ $order->delivery_address }}</p>
                        <p><strong><i class="bi bi-pin-map"></i> Distrito:</strong> {{ $order->district ?? 'No especificado' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bi bi-phone"></i> Teléfono:</strong> {{ $order->phone }}</p>
                        @if($order->delivery_reference)
                            <p><strong><i class="bi bi-pin"></i> Referencia:</strong> {{ $order->delivery_reference }}</p>
                        @endif
                    </div>
                </div>

                @if($order->special_instructions)
                    <p><strong><i class="bi bi-chat-text"></i> Instrucciones especiales:</strong> {{ $order->special_instructions }}</p>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- COLUMNA DERECHA: Resumen y acciones        -->
        <!-- ========================================== -->
        <div class="col-lg-4">
            <div class="profile-card">
                <h3 class="profile-section-title">RESUMEN</h3>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>S/. {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-truck"></i> Envío</span>
                    <span>S/. {{ number_format($order->delivery_fee, 2) }}</span>
                </div>
                @if($order->delivery_distance)
                <div class="d-flex justify-content-between mb-2 text-muted small">
                    <span><i class="bi bi-rulers"></i> Distancia</span>
                    <span>{{ number_format($order->delivery_distance, 1) }} km</span>
                </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>TOTAL</span>
                    <span class="text-success">S/. {{ number_format($order->total, 2) }}</span>
                </div>

                <!-- BOTÓN CANCELAR (unificado) -->
                @if($order->canBeCancelledByUser())
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn-retro-danger w-100" onclick="return confirm('¿Cancelar este pedido? Los productos volverán a tu carrito.')">
                            <i class="bi bi-x-circle"></i> CANCELAR PEDIDO
                        </button>
                    </form>
                @endif

                <a href="{{ route('orders.index') }}" class="btn-retro-secondary w-100 mt-2">
                    <i class="bi bi-arrow-left"></i> MIS PEDIDOS
                </a>
            </div>

            <!-- ========================================== -->
            <!-- MAPA DE SEGUIMIENTO (Repartidor)           -->
            <!-- ========================================== -->
            @if($order->isDelivering() && $order->delivery_person_lat && $order->delivery_person_lng)
                <div class="profile-card mt-3">
                    <h3 class="profile-section-title"><i class="bi bi-truck"></i> REPARTIDOR EN CAMINO</h3>
                    <div id="deliveryMap" style="height: 250px; border-radius: 8px; margin-bottom: 0.5rem;"></div>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-clock"></i> Última actualización:
                        @if($order->last_location_update)
                            {{ \Carbon\Carbon::parse($order->last_location_update)->diffForHumans() }}
                        @else
                            Hace unos momentos
                        @endif
                    </p>
                    <p class="text-muted small">
                        <i class="bi bi-geo-alt"></i> El repartidor está en camino a tu dirección.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($order->isDelivering() && $order->delivery_person_lat && $order->delivery_person_lng)
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const order = @json($order);
        const location = {
            lat: parseFloat(order.delivery_person_lat),
            lng: parseFloat(order.delivery_person_lng)
        };

        if (location.lat && location.lng) {
            // Inicializar mapa
            const map = L.map('deliveryMap').setView([location.lat, location.lng], 15);

            // Capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Icono personalizado para el repartidor
            const deliveryIcon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Marcador del repartidor
            const marker = L.marker([location.lat, location.lng], { icon: deliveryIcon })
                .addTo(map)
                .bindPopup('🚚 Repartidor en camino')
                .openPopup();

            // Si el pedido tiene coordenadas de entrega, mostrarlas también
            if (order.address_lat && order.address_lng) {
                const destLat = parseFloat(order.address_lat);
                const destLng = parseFloat(order.address_lng);
                if (destLat && destLng) {
                    L.marker([destLat, destLng], { icon: deliveryIcon })
                        .addTo(map)
                        .bindPopup('📦 Punto de entrega')
                        .openPopup();

                    // Ajustar el mapa para mostrar ambos puntos
                    const bounds = L.latLngBounds(
                        [location.lat, location.lng],
                        [destLat, destLng]
                    );
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            }
        }
    });
</script>
@endif
@endpush
