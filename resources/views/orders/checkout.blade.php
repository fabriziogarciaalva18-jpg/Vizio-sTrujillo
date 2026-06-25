@extends('layouts.retro')

@section('title', 'Checkout - Vizio\'s')

@section('content')
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
        $delivery_fee = 0;
        $total = $subtotal + $delivery_fee;
    @endphp

    @if(empty($cart))
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 4rem; color: #9E9890;"></i>
        <p class="mt-3 text-muted">Tu carrito está vacío</p>
        <a href="{{ route('catalog') }}" class="btn-retro-primary mt-2">VER CATÁLOGO</a>
    </div>
    @else
    <div class="row">
        <div class="col-lg-7">
            <div class="profile-card">
                <h3 class="profile-section-title"><i class="bi bi-geo-alt me-1"></i> DATOS DE ENTREGA</h3>

                @if ($errors->any())
                    <div class="alert alert-danger mb-3" style="background: #FEE2E2; color: #991B1B;">
                        <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf

                    <!-- ===== TIPO DE ENTREGA ===== -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-truck me-1"></i> Tipo de entrega</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_type" id="deliveryTypeDelivery" value="delivery" checked>
                                <label class="form-check-label" for="deliveryTypeDelivery"><i class="bi bi-truck me-1"></i> Entrega a domicilio</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_type" id="deliveryTypePickup" value="pickup">
                                <label class="form-check-label" for="deliveryTypePickup"><i class="bi bi-shop me-1"></i> Recoger en tienda</label>
                            </div>
                        </div>
                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> Dirección del local: Los Cedros 154, Víctor Larco Herrera</small>
                    </div>

                    <!-- ===== DIRECCIÓN (visible solo para delivery) ===== -->
                    <div id="deliveryAddressFields">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-house me-1"></i> DIRECCIÓN DE ENTREGA *</label>
                            <div class="input-group">
                                <input type="text" name="delivery_address" id="deliveryAddress" class="form-control form-control-retro" placeholder="Escribe tu dirección..." required>
                                <button type="button" class="btn-retro-secondary" id="verifyAddressBtn"><i class="bi bi-check-circle"></i> Verificar</button>
                            </div>
                            <small class="text-muted">Ej: Av. España 123, Urb. La Merced, Trujillo</small>
                            <div id="locationStatus" class="mt-2 small text-muted"></div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-pin-map me-1"></i> DISTRITO *</label>
                                <input type="text" name="district" id="district" class="form-control form-control-retro" placeholder="Ej: Víctor Larco, Trujillo, etc." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-pin me-1"></i> PUNTO DE REFERENCIA</label>
                                <input type="text" name="reference_point" class="form-control form-control-retro" placeholder="Ej: Cerca al parque, frente a la iglesia...">
                            </div>
                        </div>

                        <!-- Campos ocultos para coordenadas -->
                        <input type="hidden" name="latitude" id="latitude" value="">
                        <input type="hidden" name="longitude" id="longitude" value="">

                        <!-- Mapa (se muestra solo si la API de Google está disponible) -->
                        <div id="mapContainer" class="mt-3" style="height: 250px; border: 1px solid var(--gray-200); border-radius: 8px; display: none;">
                            <div id="map" style="height: 100%; width: 100%; border-radius: 8px;"></div>
                        </div>
                    </div>

                    <!-- ===== TELÉFONO ===== -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-phone me-1"></i> TELÉFONO (Celular) *</label>
                            <input type="tel" name="phone" class="form-control form-control-retro" placeholder="987654321" required pattern="[0-9]{9}">
                            <small class="text-muted">9 dígitos, solo números. Ej: 987654321</small>
                            @error('phone')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-calendar me-1"></i> FECHA DE ENTREGA *</label>
                            <input type="date" name="delivery_date" class="form-control form-control-retro" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label"><i class="bi bi-credit-card me-1"></i> MÉTODO DE PAGO *</label>
                        <select name="payment_method" class="form-select form-control-retro" required>
                            <option value="yape"><i class="bi bi-phone me-1"></i> YAPE</option>
                            <option value="plin"><i class="bi bi-phone me-1"></i> PLIN</option>
                            <option value="transferencia"><i class="bi bi-bank me-1"></i> TRANSFERENCIA BANCARIA</option>
                            <option value="contraentrega"><i class="bi bi-cash-stack me-1"></i> CONTRA ENTREGA</option>
                        </select>
                    </div>

                    <div class="mt-3">
                        <label class="form-label"><i class="bi bi-chat-text me-1"></i> INSTRUCCIONES ESPECIALES</label>
                        <textarea name="special_instructions" class="form-control form-control-retro" rows="2"></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-retro-primary w-100 py-2" id="submitCheckout">
                            <i class="bi bi-credit-card me-1"></i> CONTINUAR AL PAGO
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===== RESUMEN ===== -->
        <div class="col-lg-5">
            <div class="profile-card sticky-top" style="top: 90px;">
                <h3 class="profile-section-title"><i class="bi bi-receipt me-1"></i> RESUMEN</h3>
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
                <div class="d-flex justify-content-between" id="shippingRow">
                    <span><i class="bi bi-truck me-1"></i> Envío</span>
                    <span id="shippingDisplay">S/. 0.00</span>
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

@push('scripts')
<!-- Cargar Google Maps SOLO si hay API Key configurada -->
@if(config('services.google.maps_key'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
@endif

<script>
    let autocomplete;
    let map;
    let marker;
    let geocoder;
    let shippingCost = 0;
    let googleMapsLoaded = false;

    // Inicializar solo si Google Maps está disponible
    function initAutocomplete() {
        if (typeof google === 'undefined' || !google.maps) {
            console.warn('Google Maps no disponible. Usando validación manual.');
            return;
        }
        googleMapsLoaded = true;
        const input = document.getElementById('deliveryAddress');
        if (!input) return;

        const options = {
            componentRestrictions: { country: 'pe' },
            fields: ['address_components', 'geometry', 'formatted_address'],
        };

        try {
            autocomplete = new google.maps.places.Autocomplete(input, options);
            geocoder = new google.maps.Geocoder();

            // Crear mapa oculto
            const mapContainer = document.getElementById('mapContainer');
            if (mapContainer) {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: {{ config('shipping.store.lat', -8.1191) }}, lng: {{ config('shipping.store.lng', -79.0330) }} },
                    zoom: 13,
                    mapTypeId: 'roadmap',
                });
                marker = new google.maps.Marker({
                    position: { lat: {{ config('shipping.store.lat', -8.1191) }}, lng: {{ config('shipping.store.lng', -79.0330) }} },
                    map: map,
                    title: 'Vizio\'s - Los Cedros 154',
                });
            }

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    document.getElementById('locationStatus').innerHTML = '<i class="bi bi-exclamation-triangle"></i> Dirección no válida.';
                    return;
                }
                processPlace(place);
            });

        } catch (e) {
            console.warn('Error al inicializar Google Maps:', e);
            googleMapsLoaded = false;
        }
    }

    function processPlace(place) {
        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('locationStatus').innerHTML = '<i class="bi bi-check-circle" style="color: #166534;"></i> Ubicación válida en La Libertad.';
        document.getElementById('deliveryAddress').dataset.geocoded = 'true';

        if (map && marker) {
            const mapContainer = document.getElementById('mapContainer');
            mapContainer.style.display = 'block';
            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);
        }

        // Actualizar distrito
        const districtInput = document.getElementById('district');
        if (!districtInput.value) {
            const components = place.address_components || [];
            for (const comp of components) {
                if (comp.types.includes('sublocality') || comp.types.includes('locality')) {
                    districtInput.value = comp.long_name;
                    break;
                }
            }
        }

        calculateShipping(lat, lng);
    }

    // =============================================
    // VALIDACIÓN MANUAL (fallback sin Google Maps)
    // =============================================
    function verifyAddressManually() {
        const address = document.getElementById('deliveryAddress').value;
        const district = document.getElementById('district').value;
        if (!address || !district) {
            document.getElementById('locationStatus').innerHTML = '<i class="bi bi-exclamation-triangle"></i> Completa dirección y distrito.';
            return;
        }

        // Simulación de validación por distrito (lista de distritos de La Libertad)
        const validDistricts = [
            'trujillo', 'victor larco', 'la esperanza', 'el porvenir', 'florencia de mora',
            'huanchaco', 'moche', 'salaverry', 'laredo', 'santiago de cao', 'guadalupe',
            'pacasmayo', 'san pedro de loco', 'chepen', 'viru', 'chao', 'bustamante',
            'magdalena de cao', 'santa catalina', 'barranco', 'buena vista'
        ];

        const found = validDistricts.some(d => district.toLowerCase().includes(d));
        if (found) {
            document.getElementById('locationStatus').innerHTML = '<i class="bi bi-check-circle" style="color: #166534;"></i> Distrito válido en La Libertad.';
            document.getElementById('latitude').value = '-8.1191'; // coordenada aproximada del local
            document.getElementById('longitude').value = '-79.0330';
            document.getElementById('deliveryAddress').dataset.geocoded = 'true';
            calculateShipping(-8.1191, -79.0330); // distancia 0 (fallback)
        } else {
            document.getElementById('locationStatus').innerHTML = '<i class="bi bi-x-circle" style="color: #991B1B;"></i> Distrito no válido para La Libertad. Verifica.';
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }
    }

    // =============================================
    // CÁLCULO DE ENVÍO (Haversine en JS)
    // =============================================
    function calculateShipping(lat, lng) {
        const storeLat = {{ config('shipping.store.lat', -8.1191) }};
        const storeLng = {{ config('shipping.store.lng', -79.0330) }};

        const distance = haversineDistance(storeLat, storeLng, parseFloat(lat), parseFloat(lng));
        let cost = 0;
        const rates = @json(config('shipping.rates'));

        for (const rate of rates) {
            if (distance <= rate.max_distance) {
                cost = rate.price;
                break;
            }
        }

        shippingCost = cost;
        document.getElementById('shippingDisplay').innerText = 'S/. ' + cost.toFixed(2);
        updateTotal();
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function updateTotal() {
        const subtotalText = document.getElementById('subtotalDisplay').innerText.replace('S/. ', '').replace(',', '');
        const subtotal = parseFloat(subtotalText) || 0;
        const total = subtotal + shippingCost;
        document.getElementById('totalDisplay').innerText = 'S/. ' + total.toFixed(2);
    }

    // =============================================
    // EVENTOS Y TOGGLES
    // =============================================
    document.addEventListener('DOMContentLoaded', function() {
        // Botón de verificación manual (si no hay Google Maps)
        const verifyBtn = document.getElementById('verifyAddressBtn');
        if (verifyBtn) {
            verifyBtn.addEventListener('click', function() {
                if (googleMapsLoaded && autocomplete) {
                    // Si Google Maps está cargado, forzar geocodificación
                    const address = document.getElementById('deliveryAddress').value;
                    if (address && geocoder) {
                        geocoder.geocode({ address: address, componentRestrictions: { country: 'pe' } }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                const place = results[0];
                                const lat = place.geometry.location.lat();
                                const lng = place.geometry.location.lng();
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lng;
                                document.getElementById('locationStatus').innerHTML = '<i class="bi bi-check-circle" style="color: #166534;"></i> Ubicación encontrada.';
                                calculateShipping(lat, lng);
                            } else {
                                document.getElementById('locationStatus').innerHTML = '<i class="bi bi-x-circle" style="color: #991B1B;"></i> Dirección no válida. Intenta nuevamente.';
                            }
                        });
                    } else {
                        verifyAddressManually();
                    }
                } else {
                    verifyAddressManually();
                }
            });
        }

        // Toggle entre entrega y recogida
        document.querySelectorAll('input[name="delivery_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const deliveryFields = document.getElementById('deliveryAddressFields');
                const shippingRow = document.getElementById('shippingRow');

                if (this.value === 'pickup') {
                    deliveryFields.style.display = 'none';
                    shippingRow.style.display = 'none';
                    shippingCost = 0;
                    document.getElementById('shippingDisplay').innerText = 'S/. 0.00';
                    document.getElementById('deliveryAddress').removeAttribute('required');
                    document.getElementById('district').removeAttribute('required');
                } else {
                    deliveryFields.style.display = 'block';
                    shippingRow.style.display = 'flex';
                    document.getElementById('deliveryAddress').setAttribute('required', 'required');
                    document.getElementById('district').setAttribute('required', 'required');
                    // Recalcular si hay datos
                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;
                    if (lat && lng) {
                        calculateShipping(parseFloat(lat), parseFloat(lng));
                    } else {
                        shippingCost = 0;
                        document.getElementById('shippingDisplay').innerText = 'S/. 0.00';
                        updateTotal();
                    }
                }
            });
        });

        // Inicializar estado
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        if (lat && lng) {
            calculateShipping(parseFloat(lat), parseFloat(lng));
        }
    });

    // Si Google Maps no carga, mostrar mensaje pero permitir validación manual
    window.initAutocomplete = initAutocomplete;
</script>
@endpush