@extends('layouts.retro')

@section('title', 'Detalle de Entrega #' . $order->order_number)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="section-title" style="font-size: 1.5rem;">← DETALLE DE ENTREGA →</h1>
        <a href="{{ route('delivery.dashboard') }}" class="btn-retro-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B;">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <!-- Columna izquierda: Información del pedido -->
        <div class="col-lg-6">
            <div class="profile-card">
                <h3 class="profile-section-title">INFORMACIÓN DEL PEDIDO</h3>

                <p><strong><i class="bi bi-hash"></i> Pedido:</strong> #{{ $order->order_number }}</p>
                <p><strong><i class="bi bi-person"></i> Cliente:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                <p><strong><i class="bi bi-phone"></i> Teléfono:</strong> {{ $order->phone }}</p>
                <p><strong><i class="bi bi-calendar"></i> Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                <p><strong><i class="bi bi-truck"></i> Estado:</strong>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ $order->status == 'preparing' ? 'Preparado' : 'En camino' }}
                    </span>
                </p>
                <p><strong><i class="bi bi-credit-card"></i> Método de pago:</strong> {{ strtoupper($order->payment_method) }}</p>
                <p><strong><i class="bi bi-currency-dollar"></i> Total:</strong> S/. {{ number_format($order->total, 2) }}</p>

                <hr>

                <h5><i class="bi bi-geo-alt"></i> Ubicación de entrega</h5>
                <p><strong>Dirección:</strong> {{ $order->delivery_address }}</p>
                <p><strong>Distrito:</strong> {{ $order->district ?? 'No especificado' }}</p>
                @if($order->delivery_reference)
                    <div class="alert alert-retro mt-2" style="background: #E0F2FE; color: #075985; border: 1px solid #7DD3FC;">
                        <i class="bi bi-pin"></i> <strong>Referencia:</strong> {{ $order->delivery_reference }}
                    </div>
                @endif

                <hr>

                <h5><i class="bi bi-box-seam"></i> Productos</h5>
                <ul class="list-unstyled">
                    @foreach($order->items as $item)
                        <li>{{ $item->product_name }} x{{ $item->quantity }} – S/. {{ number_format($item->subtotal, 2) }}</li>
                    @endforeach
                </ul>
                <p class="fw-bold">Total: S/. {{ number_format($order->total, 2) }}</p>

                @if($order->special_instructions)
                    <p><strong><i class="bi bi-chat-text"></i> Instrucciones:</strong> {{ $order->special_instructions }}</p>
                @endif
            </div>
        </div>

        <!-- Columna derecha: Mapa y acciones -->
        <div class="col-lg-6">
            <div class="profile-card">
                <h3 class="profile-section-title"><i class="bi bi-map"></i> UBICACIÓN</h3>

                <div id="deliveryMap" style="height: 300px; border-radius: 8px; margin-bottom: 1rem;"></div>

                <div class="d-grid gap-2">
                    @if($order->status == 'preparing' || $order->status == 'delivering')
                        <button id="shareLocationBtn" class="btn-retro-primary">
                            <i class="bi bi-satellite"></i> COMPARTIR UBICACIÓN EN VIVO
                        </button>
                        <button id="stopSharingBtn" class="btn-retro-danger" style="display: none;">
                            <i class="bi bi-stop-circle"></i> DETENER COMPARTIR
                        </button>
                        <small class="text-muted" id="locationStatus">Comparte tu ubicación para que el cliente pueda seguir el pedido.</small>
                    @endif

                    <form action="{{ route('delivery.confirm', $order) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn-retro-primary w-100" onclick="return confirm('¿Confirmar entrega?')">
                            <i class="bi bi-check-circle"></i> CONFIRMAR ENTREGA
                        </button>
                    </form>
                    <button type="button" class="btn-retro-danger w-100" data-bs-toggle="modal" data-bs-target="#failedModal">
                        <i class="bi bi-x-circle"></i> MARCAR COMO FALLIDA
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Fallida -->
<div class="modal fade" id="failedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-retro">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Marcar como fallida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('delivery.failed', $order) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Motivo de la falla:</p>
                    <textarea name="failure_reason" class="form-control form-control-retro" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-retro-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-retro-danger">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const order = @json($order);
    const storeLocation = @json($storeLocation);

    const map = L.map('deliveryMap').setView([-8.1120, -79.0288], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let deliveryMarker = null;
    if (order.address_lat && order.address_lng) {
        const lat = parseFloat(order.address_lat);
        const lng = parseFloat(order.address_lng);
        deliveryMarker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map).bindPopup('<i class="bi bi-geo-alt-fill"></i> Punto de entrega').openPopup();
        map.setView([lat, lng], 15);
    }

    let personMarker = null;
    if (order.delivery_person_lat && order.delivery_person_lng) {
        const lat = parseFloat(order.delivery_person_lat);
        const lng = parseFloat(order.delivery_person_lng);
        personMarker = L.marker([lat, lng]).addTo(map).bindPopup('<i class="bi bi-truck"></i> Repartidor aquí');
    }

    const shareBtn = document.getElementById('shareLocationBtn');
    const stopBtn = document.getElementById('stopSharingBtn');
    const locationStatus = document.getElementById('locationStatus');
    let watchId = null;

    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Tu navegador no soporta geolocalización.');
                return;
            }

            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (!personMarker) {
                        personMarker = L.marker([lat, lng]).addTo(map).bindPopup('<i class="bi bi-truck"></i> Repartidor aquí');
                    } else {
                        personMarker.setLatLng([lat, lng]);
                    }

                    fetch(`/delivery/orders/${order.id}/update-location`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ lat, lng })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            locationStatus.innerHTML = '<i class="bi bi-check-circle-fill"></i> Ubicación actualizada: ' +
                                new Date().toLocaleTimeString();
                        }
                    })
                    .catch(err => console.error(err));
                },
                function(error) {
                    alert('Error al obtener ubicación: ' + error.message);
                },
                { enableHighAccuracy: true }
            );

            shareBtn.style.display = 'none';
            stopBtn.style.display = 'block';
            locationStatus.innerHTML = '<i class="bi bi-arrow-repeat"></i> Compartiendo ubicación...';
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', function() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            shareBtn.style.display = 'block';
            stopBtn.style.display = 'none';
            locationStatus.innerHTML = '<i class="bi bi-stop-circle-fill"></i> Compartir detenido.';
        });
    }
});
</script>
@endpush
