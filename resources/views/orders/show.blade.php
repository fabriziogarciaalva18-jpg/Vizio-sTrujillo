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
            @if($order->isDelivering() && $order->delivery_person_id)
                <div class="profile-card mt-3">
                    <h3 class="profile-section-title"><i class="bi bi-truck"></i> REPARTIDOR EN CAMINO</h3>
                    <div id="clientMap" style="height: 250px; border-radius: 8px; margin-bottom: 0.5rem;"></div>
                    <p class="text-muted small" id="locationUpdateTime">
                        <i class="bi bi-clock"></i> Última actualización: ahora
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- ========================================== -->
<!-- MAPA DE SEGUIMIENTO EN TIEMPO REAL        -->
<!-- ========================================== -->
@if($order->isDelivering() && $order->delivery_person_id)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderId = {{ $order->id }};

        // Inicializar mapa
        const map = L.map('clientMap').setView([-8.1120, -79.0288], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker = null;
        let deliveryMarker = null;

        // Icono para el repartidor
        const deliveryIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Icono para el punto de entrega
        const homeIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        function updateLocation() {
            fetch(`/delivery/orders/${orderId}/location`)
                .then(res => res.json())
                .then(data => {
                    const locationUpdateTime = document.getElementById('locationUpdateTime');
                    const now = new Date();

                    if (data.location && data.location.lat && data.location.lng) {
                        const lat = parseFloat(data.location.lat);
                        const lng = parseFloat(data.location.lng);

                        // Actualizar marcador del repartidor
                        if (!marker) {
                            marker = L.marker([lat, lng], { icon: deliveryIcon })
                                .addTo(map)
                                .bindPopup('<i class="bi bi-truck"></i> Repartidor en camino')
                                .openPopup();

                            // Si el pedido tiene dirección, mostrar punto de entrega
                            if (data.order && data.order.address_lat && data.order.address_lng) {
                                const destLat = parseFloat(data.order.address_lat);
                                const destLng = parseFloat(data.order.address_lng);
                                if (destLat && destLng) {
                                    deliveryMarker = L.marker([destLat, destLng], { icon: homeIcon })
                                        .addTo(map)
                                        .bindPopup('<i class="bi bi-geo-alt-fill"></i> Punto de entrega');

                                    // Ajustar vista para mostrar ambos puntos
                                    const bounds = L.latLngBounds(
                                        [lat, lng],
                                        [destLat, destLng]
                                    );
                                    map.fitBounds(bounds, { padding: [50, 50] });
                                }
                            } else {
                                map.setView([lat, lng], 15);
                            }
                        } else {
                            marker.setLatLng([lat, lng]);
                            // Actualizar popup con la nueva ubicación
                            marker.bindPopup('<i class="bi bi-truck"></i> Repartidor en camino - Actualizado: ' + now.toLocaleTimeString());
                        }

                        // Actualizar timestamp
                        if (locationUpdateTime) {
                            locationUpdateTime.innerHTML = '<i class="bi bi-clock"></i> Última actualización: ' + now.toLocaleTimeString();
                        }
                    } else {
                        if (locationUpdateTime) {
                            locationUpdateTime.innerHTML = '<i class="bi bi-exclamation-circle"></i> Esperando ubicación del repartidor...';
                        }
                    }
                })
                .catch(err => {
                    console.error('Error al obtener ubicación del repartidor:', err);
                    const locationUpdateTime = document.getElementById('locationUpdateTime');
                    if (locationUpdateTime) {
                        locationUpdateTime.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Error al actualizar ubicación';
                    }
                });
        }

        // Actualizar cada 10 segundos
        updateLocation();
        setInterval(updateLocation, 10000);
    });
    </script>
@endif
@endpush
