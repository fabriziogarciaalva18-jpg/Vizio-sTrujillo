@extends('layouts.retro')

@section('title', 'Checkout - Vizio\'s')

@section('content')
@if ($errors->any())
    <div class="alert alert-retro" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← FINALIZAR PEDIDO →</h1>
        <div class="section-divider"></div>
    </div>

    @php
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'];
        }
    @endphp

    @if(empty($cart))
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 4rem; color: #9E9890;"></i>
        <p class="mt-3 text-muted">Tu carrito está vacío</p>
        <a href="{{ route('catalog') }}" class="btn-retro-primary mt-2">
            <i class="bi bi-grid-3x3-gap-fill"></i> VER CATÁLOGO
        </a>
    </div>
    @else
    <div class="row">
        <div class="col-lg-7">
            <div class="profile-card">
                <h3 class="profile-section-title">DATOS DE ENTREGA</h3>
                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf

                    <!-- ========================================== -->
                    <!-- 1. TIPO DE ENTREGA                          -->
                    <!-- ========================================== -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-truck"></i> Tipo de entrega *</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_type" id="deliveryPickup" value="pickup" checked>
                                <label class="form-check-label" for="deliveryPickup">
                                    <i class="bi bi-shop"></i> Recojo en tienda
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_type" id="deliveryDelivery" value="delivery">
                                <label class="form-check-label" for="deliveryDelivery">
                                    <i class="bi bi-truck"></i> Envío a domicilio
                                </label>
                            </div>
                        </div>
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> Tienda: Los Cedros 154, Víctor Larco Herrera, Trujillo</small>
                    </div>

                    <!-- ========================================== -->
                    <!-- 2. UBICACIÓN (solo para delivery)         -->
                    <!-- ========================================== -->
                    <div id="deliveryFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-search"></i> Buscar dirección *</label>
                            <input type="text" id="addressSearch" class="form-control form-control-retro" placeholder="Escribe tu dirección (ej: Av. La Marina 123, Trujillo)">
                            <div id="addressSuggestions" class="list-group mt-1" style="position: absolute; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto;"></div>
                            <div id="locationValidation" class="mt-1"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-geo"></i> Dirección seleccionada</label>
                            <input type="text" name="delivery_address" id="deliveryAddress" class="form-control form-control-retro" readonly>
                            <input type="hidden" name="address_lat" id="addressLat" value="">
                            <input type="hidden" name="address_lng" id="addressLng" value="">
                            <input type="hidden" name="delivery_distance" id="deliveryDistance" value="">
                            <input type="hidden" name="delivery_fee" id="deliveryFee" value="">
                            <small class="text-muted">Selecciona una dirección de la lista de sugerencias.</small>
                        </div>

                        <!-- ========================================== -->
                        <!-- 3. MAPA (AQUÍ VA EL MAPA)                -->
                        <!-- ========================================== -->
                        <div class="mb-3" id="mapContainer" style="display: none;">
                            <label class="form-label"><i class="bi bi-geo-alt"></i> Ubicación exacta en el mapa</label>
                            <div id="locationMap" style="height: 300px; border-radius: 8px; border: 2px solid var(--gray-200);"></div>
                            <small class="text-muted">Arrastra el marcador para ajustar la ubicación exacta.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-pin-map"></i> Distrito</label>
                            <input type="text" name="district" class="form-control form-control-retro" placeholder="Ej: Víctor Larco, Trujillo" id="districtInput">
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- 4. TELÉFONO                              -->
                    <!-- ========================================== -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-phone"></i> Teléfono *</label>
                        <input type="tel" name="phone" class="form-control form-control-retro" placeholder="987654321" required>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Número de 9 dígitos que comienza con 9 (ej: 987654321)</small>
                        @error('phone')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- ========================================== -->
                    <!-- 5. FECHA DE ENTREGA                      -->
                    <!-- ========================================== -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-calendar"></i> Fecha de entrega *</label>
                        <input type="date" name="delivery_date" class="form-control form-control-retro" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>

                    <!-- ========================================== -->
                    <!-- 6. MÉTODO DE PAGO                       -->
                    <!-- ========================================== -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-credit-card"></i> Método de pago *</label>
                        <select name="payment_method" class="form-select form-control-retro" required>
                            <option value="yape"><i class="bi bi-phone"></i> YAPE</option>
                            <option value="plin"><i class="bi bi-phone"></i> PLIN</option>
                            <option value="transferencia"><i class="bi bi-bank"></i> Transferencia bancaria</option>
                            <option value="contraentrega"><i class="bi bi-cash-stack"></i> Pago en efectivo (contra entrega)</option>
                        </select>
                    </div>

                    <!-- ========================================== -->
                    <!-- 7. REFERENCIA DE ENTREGA                 -->
                    <!-- ========================================== -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-pin"></i> Referencia de entrega</label>
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <button type="button" class="btn-retro-secondary btn-sm w-100 ref-quick" data-ref="Dejar en la puerta principal">
                                    <i class="bi bi-door-open"></i> Puerta principal
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn-retro-secondary btn-sm w-100 ref-quick" data-ref="Timbrar al departamento">
                                    <i class="bi bi-bell"></i> Timbrar
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn-retro-secondary btn-sm w-100 ref-quick" data-ref="Entregar al conserje">
                                    <i class="bi bi-person"></i> Conserje
                                </button>
                            </div>
                        </div>
                        <textarea name="delivery_reference" class="form-control form-control-retro" rows="2" placeholder="Ej: Dejar en la puerta, timbrar al 2B, entregar al conserje..."></textarea>
                        <small class="text-muted"><i class="bi bi-info-circle"></i> Indicaciones para que el repartidor encuentre fácilmente el lugar.</small>
                    </div>

                    <!-- ========================================== -->
                    <!-- 8. INSTRUCCIONES ESPECIALES              -->
                    <!-- ========================================== -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-chat-text"></i> Instrucciones especiales</label>
                        <textarea name="special_instructions" class="form-control form-control-retro" rows="3"></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-retro-primary w-100 py-2" id="submitBtn">
                            <i class="bi bi-credit-card"></i> CONTINUAR AL PAGO
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 9. RESUMEN DEL PEDIDO                     -->
        <!-- ========================================== -->
        <div class="col-lg-5">
            <div class="profile-card">
                <h3 class="profile-section-title"><i class="bi bi-receipt"></i> RESUMEN</h3>
                @foreach($cart as $item)
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                    <span>S/. {{ number_format(($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'], 2) }}</span>
                </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span id="subtotalDisplay">S/. {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between" id="deliveryFeeRow">
                    <span><i class="bi bi-truck"></i> Envío</span>
                    <span id="deliveryFeeDisplay">S/. 0.00</span>
                </div>
                <div class="d-flex justify-content-between text-muted small" id="distanceDisplay" style="display: none;">
                    <span><i class="bi bi-rulers"></i> Distancia</span>
                    <span id="distanceText">0 km</span>
                </div>
                <div class="d-flex justify-content-between mt-2" id="referenceDisplay" style="display: none;">
                    <span><i class="bi bi-pin"></i> Referencia</span>
                    <span id="referenceText" class="text-muted small"></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>TOTAL</span>
                    <span class="text-success" id="totalDisplay">S/. {{ number_format($subtotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

<!-- ========================================== -->
<!-- 10. SCRIPTS (AQUÍ VA TODO EL JAVASCRIPT)  -->
<!-- ========================================== -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pickupRadio = document.getElementById('deliveryPickup');
    const deliveryRadio = document.getElementById('deliveryDelivery');
    const deliveryFields = document.getElementById('deliveryFields');
    const addressSearch = document.getElementById('addressSearch');
    const suggestions = document.getElementById('addressSuggestions');
    const deliveryAddress = document.getElementById('deliveryAddress');
    const addressLat = document.getElementById('addressLat');
    const addressLng = document.getElementById('addressLng');
    const deliveryDistance = document.getElementById('deliveryDistance');
    const deliveryFee = document.getElementById('deliveryFee');
    const deliveryFeeDisplay = document.getElementById('deliveryFeeDisplay');
    const distanceDisplay = document.getElementById('distanceDisplay');
    const distanceText = document.getElementById('distanceText');
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const locationValidation = document.getElementById('locationValidation');
    const districtInput = document.getElementById('districtInput');
    const referenceDisplay = document.getElementById('referenceDisplay');
    const referenceText = document.getElementById('referenceText');

    const storeLat = {{ config('delivery.store.lat') }};
    const storeLng = {{ config('delivery.store.lng') }};

    let selectedLat = null;
    let selectedLng = null;
    let isAddressValid = false;

    // =============================================
    // INICIALIZAR: DESHABILITAR CAMPOS DELIVERY
    // =============================================
    deliveryFields.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);

    // =============================================
    // REFERENCIA EN TIEMPO REAL
    // =============================================
    const refTextarea = document.querySelector('textarea[name="delivery_reference"]');
    if (refTextarea) {
        refTextarea.addEventListener('input', function() {
            const ref = this.value.trim();
            if (ref) {
                referenceDisplay.style.display = 'flex';
                referenceText.textContent = ref;
            } else {
                referenceDisplay.style.display = 'none';
            }
        });
    }

    // =============================================
    // TOGGLE PICKUP / DELIVERY
    // =============================================
    pickupRadio.addEventListener('change', function() {
        if (this.checked) {
            deliveryFields.style.display = 'none';
            deliveryFields.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
            deliveryFeeDisplay.textContent = 'S/. 0.00';
            distanceDisplay.style.display = 'none';
            isAddressValid = true;
            updateTotal();
        }
    });

    deliveryRadio.addEventListener('change', function() {
        if (this.checked) {
            deliveryFields.style.display = 'block';
            deliveryFields.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
            isAddressValid = false;
            if (selectedLat && selectedLng) {
                calculateDelivery(selectedLat, selectedLng);
            }
        }
    });

    // =============================================
    // BÚSQUEDA DE DIRECCIONES (NOMINATIM)
    // =============================================
    let searchTimeout;
    addressSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        if (query.length < 3) {
            suggestions.innerHTML = '';
            isAddressValid = false;
            locationValidation.innerHTML = '';
            deliveryAddress.value = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}, Trujillo, La Libertad, Peru&format=json&limit=5&addressdetails=1&countrycodes=pe`)
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    if (data.length === 0) {
                        suggestions.innerHTML = '<div class="list-group-item text-muted">No se encontraron direcciones. Intenta con otro término.</div>';
                        return;
                    }
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'list-group-item list-group-item-action';
                        div.textContent = item.display_name;
                        div.style.cursor = 'pointer';
                        div.addEventListener('click', function() {
                            addressSearch.value = item.display_name;
                            deliveryAddress.value = item.display_name;
                            selectedLat = parseFloat(item.lat);
                            selectedLng = parseFloat(item.lon);
                            addressLat.value = selectedLat;
                            addressLng.value = selectedLng;
                            suggestions.innerHTML = '';

                            // Validar región
                            const addressData = item.address || {};
                            const region = addressData.state || addressData.region || '';
                            const city = addressData.city || addressData.town || addressData.village || '';
                            if (!region.toLowerCase().includes('la libertad') && !region.toLowerCase().includes('trujillo') && !city.toLowerCase().includes('trujillo')) {
                                locationValidation.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> La dirección debe estar en La Libertad.</span>';
                                deliveryAddress.value = '';
                                selectedLat = null;
                                selectedLng = null;
                                addressLat.value = '';
                                addressLng.value = '';
                                isAddressValid = false;
                                return;
                            }

                            locationValidation.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Ubicación válida en La Libertad.</span>';
                            isAddressValid = true;

                            // Distrito
                            const district = addressData.suburb || addressData.city_district || addressData.town || '';
                            if (district) {
                                districtInput.value = district;
                            }

                            // INICIALIZAR MAPA CON LAS COORDENADAS
                            initMap(selectedLat, selectedLng);

                            if (deliveryRadio.checked) {
                                calculateDelivery(selectedLat, selectedLng);
                            }
                        });
                        suggestions.appendChild(div);
                    });
                })
                .catch(() => {
                    suggestions.innerHTML = '<div class="list-group-item text-danger"><i class="bi bi-exclamation-triangle"></i> Error al buscar direcciones. Intenta nuevamente.</div>';
                });
        }, 500);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#addressSearch') && !e.target.closest('#addressSuggestions')) {
            suggestions.innerHTML = '';
        }
    });

    // =============================================
    // MAPA CON MARCADOR ARRASTRABLE
    // =============================================
    let map, marker;

    function initMap(lat = -8.1120, lng = -79.0288) {
        if (typeof L === 'undefined') {
            // Cargar Leaflet si no está cargado
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => {
                createMap(lat, lng);
            };
            document.body.appendChild(script);
        } else {
            createMap(lat, lng);
        }
    }

    function createMap(lat, lng) {
        const container = document.getElementById('locationMap');
        if (!container) return;

        // Destruir mapa anterior si existe
        if (map) {
            map.remove();
        }

        map = L.map('locationMap').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const icon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        marker = L.marker([lat, lng], { draggable: true, icon }).addTo(map);

        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            selectedLat = pos.lat;
            selectedLng = pos.lng;
            addressLat.value = selectedLat;
            addressLng.value = selectedLng;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${selectedLat}&lon=${selectedLng}&zoom=18&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.display_name) {
                        deliveryAddress.value = data.display_name;
                        addressSearch.value = data.display_name;
                        const addressData = data.address || {};
                        const region = addressData.state || addressData.region || '';
                        const city = addressData.city || addressData.town || addressData.village || '';
                        if (region.toLowerCase().includes('la libertad') || city.toLowerCase().includes('trujillo')) {
                            locationValidation.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Ubicación válida en La Libertad.</span>';
                            isAddressValid = true;
                            const district = addressData.suburb || addressData.city_district || addressData.town || '';
                            if (district) {
                                districtInput.value = district;
                            }
                            if (deliveryRadio.checked) {
                                calculateDelivery(selectedLat, selectedLng);
                            }
                        } else {
                            locationValidation.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> La ubicación debe estar en La Libertad.</span>';
                            isAddressValid = false;
                        }
                    }
                });
        });

        document.getElementById('mapContainer').style.display = 'block';
    }

    // =============================================
    // CÁLCULO DE DISTANCIA Y COSTO DE ENVÍO
    // =============================================
    function calculateDelivery(lat, lng) {
        const url = `https://router.project-osrm.org/route/v1/driving/${storeLng},${storeLat};${lng},${lat}?overview=false`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                let distanceKm = 0;
                if (data.routes && data.routes.length > 0) {
                    distanceKm = data.routes[0].distance / 1000;
                } else {
                    distanceKm = haversineDistance(storeLat, storeLng, lat, lng);
                }
                distanceKm = Math.round(distanceKm * 100) / 100;

                const baseFee = {{ config('delivery.fee.base') }};
                const perKm = {{ config('delivery.fee.per_km') }};
                const freeDistance = {{ config('delivery.fee.free_distance') }};
                const maxDistance = {{ config('delivery.fee.max_distance') }};

                let fee = baseFee;
                let distanceToCharge = Math.max(0, distanceKm - freeDistance);
                fee += distanceToCharge * perKm;
                fee = Math.round(fee * 100) / 100;

                if (distanceKm > maxDistance) {
                    deliveryFeeDisplay.textContent = 'No disponible';
                    distanceText.textContent = `${distanceKm.toFixed(1)} km (supera el máximo)`;
                    distanceDisplay.style.display = 'block';
                    deliveryFee.value = 0;
                    deliveryDistance.value = distanceKm;
                    isAddressValid = false;
                    updateTotal();
                    return;
                }

                deliveryFeeDisplay.textContent = `S/. ${fee.toFixed(2)}`;
                distanceText.textContent = `${distanceKm.toFixed(1)} km`;
                distanceDisplay.style.display = 'block';
                deliveryFee.value = fee;
                deliveryDistance.value = distanceKm;
                isAddressValid = true;
                updateTotal();
            })
            .catch(() => {
                const distanceKm = haversineDistance(storeLat, storeLng, lat, lng);
                const baseFee = {{ config('delivery.fee.base') }};
                const perKm = {{ config('delivery.fee.per_km') }};
                const freeDistance = {{ config('delivery.fee.free_distance') }};
                const maxDistance = {{ config('delivery.fee.max_distance') }};

                let fee = baseFee;
                let distanceToCharge = Math.max(0, distanceKm - freeDistance);
                fee += distanceToCharge * perKm;
                fee = Math.round(fee * 100) / 100;

                if (distanceKm > maxDistance) {
                    deliveryFeeDisplay.textContent = 'No disponible';
                    distanceText.textContent = `${distanceKm.toFixed(1)} km (supera el máximo)`;
                    distanceDisplay.style.display = 'block';
                    deliveryFee.value = 0;
                    deliveryDistance.value = distanceKm;
                    isAddressValid = false;
                    updateTotal();
                    return;
                }

                deliveryFeeDisplay.textContent = `S/. ${fee.toFixed(2)}`;
                distanceText.textContent = `${distanceKm.toFixed(1)} km`;
                distanceDisplay.style.display = 'block';
                deliveryFee.value = fee;
                deliveryDistance.value = distanceKm;
                isAddressValid = true;
                updateTotal();
            });
    }

    function haversineDistance(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    // =============================================
    // ACTUALIZAR TOTAL
    // =============================================
    function updateTotal() {
        const subtotalText = subtotalDisplay.textContent.replace('S/. ', '').replace(',', '');
        const subtotal = parseFloat(subtotalText) || 0;
        const feeText = deliveryFeeDisplay.textContent.replace('S/. ', '').replace(',', '');
        const fee = parseFloat(feeText) || 0;
        const total = subtotal + fee;
        totalDisplay.textContent = `S/. ${total.toFixed(2)}`;
    }

    // =============================================
    // BOTONES DE REFERENCIA RÁPIDA
    // =============================================
    document.querySelectorAll('.ref-quick').forEach(btn => {
        btn.addEventListener('click', function() {
            const ref = this.dataset.ref;
            const textarea = document.querySelector('textarea[name="delivery_reference"]');
            if (textarea) {
                if (textarea.value.trim()) {
                    textarea.value += '\n' + ref;
                } else {
                    textarea.value = ref;
                }
                textarea.dispatchEvent(new Event('input'));
            }
        });
    });

    // =============================================
    // VALIDACIÓN ANTES DE ENVIAR
    // =============================================
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        if (deliveryRadio.checked && !isAddressValid) {
            e.preventDefault();
            alert('Por favor, selecciona una dirección válida de la lista de sugerencias.');
            return;
        }
    });
});
</script>
@endpush
