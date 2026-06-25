@extends('layouts.retro')

@section('title', $product->name . ' - Vizio\'s')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        <!-- Imagen -->
        <div class="col-lg-6">
            <div class="retro-card p-4 text-center">
                <img src="{{ $product->image_url }}"
                     alt="{{ $product->name }}"
                     class="img-fluid rounded"
                     style="max-height: 450px; object-fit: contain;">
            </div>
        </div>

        <!-- Detalles y personalización -->
        <div class="col-lg-6">
            <div class="retro-card p-4">
                <h1 class="product-title" style="font-size: 2rem; font-weight: 600;">{{ $product->name }}</h1>
                <p class="text-muted mt-2" style="font-size: 0.95rem; line-height: 1.6;">{{ $product->description }}</p>
                <hr class="retro-divider">

                <form action="{{ route('cart.add') }}" method="POST" id="productForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="row g-3">
                        <!-- ========================================= -->
                        <!-- OPCIONES DE PERSONALIZACIÓN (opcionales) -->
                        <!-- ========================================= -->
                        @foreach($configurations as $type => $items)
                            @php
                                $icon = match($type) {
                                    'size' => 'bi-rulers',
                                    'layers' => 'bi-layers',
                                    'flavor' => 'bi-droplet',
                                    'filling' => 'bi-egg-fried',
                                    'covering' => 'bi-brush',
                                    'shape' => 'bi-shapes',
                                    'color' => 'bi-palette',
                                    'toppings' => 'bi-stars',
                                    'decoration' => 'bi-gem',
                                    default => 'bi-tag'
                                };
                                $label = match($type) {
                                    'size' => 'Tamaño',
                                    'layers' => 'Número de pisos',
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

                            <div class="col-12">
                                <!-- Checkbox para activar/desactivar el grupo -->
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="checkbox" class="group-toggle" data-group="{{ $type }}" id="toggle_{{ $type }}">
                                    <label class="form-label fw-bold mb-0" for="toggle_{{ $type }}" style="cursor: pointer;">
                                        <i class="bi {{ $icon }} me-1"></i> {{ $label }}
                                    </label>
                                    <span class="text-muted small ms-auto" id="price_summary_{{ $type }}"></span>
                                </div>

                                <!-- Opciones del grupo (inicialmente ocultas) -->
                                <div class="group-options" id="options_{{ $type }}" style="display: none; padding-left: 1.5rem;">
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($items as $item)
                                        <label class="custom-option">
                                            <input type="radio" name="configurations[{{ $type }}]" value="{{ $item->id }}"
                                                   data-price="{{ $item->price_modifier }}" class="config-option" disabled>
                                            <span class="option-label">
                                                {{ $item->name }}
                                                @if($item->price_modifier > 0)
                                                    <span class="badge-price">+ S/. {{ number_format($item->price_modifier, 2) }}</span>
                                                @endif
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- ========================================= -->
                        <!-- MENSAJE PERSONALIZADO (siempre opcional)  -->
                        <!-- ========================================= -->
                        @if($messageConfig)
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="bi bi-chat-text me-1"></i> MENSAJE PERSONALIZADO
                                @if($messageConfig->price_modifier > 0)
                                    <span class="badge-price ms-2">+ S/. {{ number_format($messageConfig->price_modifier, 2) }}</span>
                                @endif
                            </label>
                            <textarea name="message" class="form-control form-control-retro" rows="2"
                                      placeholder="Escribe el mensaje que quieres en tu torta..."></textarea>
                            <small class="text-muted">Ej: "Feliz Cumpleaños", "Te quiero", etc.</small>
                            <input type="hidden" name="message_price" value="{{ $messageConfig->price_modifier }}" id="messagePrice">
                            <input type="hidden" name="message_config_id" value="{{ $messageConfig->id }}">
                        </div>
                        @endif

                        <!-- ========================================= -->
                        <!-- ADICIONALES (globales)                   -->
                        <!-- ========================================= -->
                        @if(isset($addons) && count($addons) > 0)
                        <div class="col-12">
                            <label class="form-label fw-bold"><i class="bi bi-plus-circle me-1"></i> ADICIONALES</label>
                            <div class="row g-2">
                                @foreach($addons as $addon)
                                <div class="col-12 col-md-6">
                                    <label class="custom-option addon-option">
                                        <input type="checkbox" name="addons[]" value="{{ $addon->id }}"
                                               data-price="{{ $addon->price }}" class="addon-checkbox">
                                        <span class="option-label">
                                            {{ $addon->name }}
                                            <small class="text-muted d-block">{{ $addon->description }}</small>
                                            <span class="badge-price">+ S/. {{ number_format($addon->price, 2) }}</span>
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Cantidad -->
                        <div class="col-12">
                            <label class="form-label fw-bold"><i class="bi bi-hash me-1"></i> CANTIDAD</label>
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" class="btn-retro-secondary btn-sm" id="decrementQty">−</button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1"
                                       class="form-control form-control-retro text-center" style="width: 80px;">
                                <button type="button" class="btn-retro-secondary btn-sm" id="incrementQty">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Total y botón -->
                    <hr class="retro-divider mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="text-muted small">Total</span>
                            <div class="h3 fw-bold text-retro" id="totalPrice">
                                S/. {{ number_format($product->base_price, 2) }}
                            </div>
                        </div>
                        <button type="submit" class="btn-retro-primary py-3 px-5">
                            <i class="bi bi-cart-plus"></i> AGREGAR AL CARRITO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-option {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        border: 1px solid var(--gray-200);
        border-radius: 6px;
        padding: 0.3rem 0.8rem;
        transition: all 0.2s;
        background: var(--white);
        margin: 0.15rem;
        user-select: none;
    }
    .custom-option:hover {
        border-color: var(--gray-400);
        background: var(--gray-50);
    }
    .custom-option input[type="radio"],
    .custom-option input[type="checkbox"] {
        display: none;
    }
    .custom-option .option-label {
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--gray-600);
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }
    .custom-option .badge-price {
        font-size: 0.6rem;
        color: var(--gray-400);
        font-weight: normal;
    }
    .custom-option input:checked + .option-label {
        color: var(--black);
    }
    .custom-option:has(input:checked) {
        border-color: var(--black);
        background: var(--gray-50);
        box-shadow: 0 0 0 1px rgba(0,0,0,0.05);
    }
    .addon-option {
        width: 100%;
        justify-content: space-between;
    }
    .text-retro {
        color: var(--black);
        font-family: 'DM Serif Display', serif;
    }
    .group-options {
        padding-left: 1.5rem;
        border-left: 2px dashed var(--gray-200);
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@push('scripts')
<script>
    // =============================================
    // FUNCIÓN PRINCIPAL DE CÁLCULO
    // =============================================
    function calculatePrice() {
        let basePrice = parseFloat({{ $product->base_price }});

        // Sumar configuraciones seleccionadas (solo de grupos activos)
        document.querySelectorAll('.group-toggle:checked').forEach(toggle => {
            const group = toggle.dataset.group;
            const checkedRadio = document.querySelector(`input[name="configurations[${group}"]:checked`);
            if (checkedRadio) {
                const price = parseFloat(checkedRadio.dataset.price) || 0;
                basePrice += price;
            }
        });

        // Sumar adicionales seleccionados
        document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price) || 0;
            basePrice += price;
        });

        // Sumar costo del mensaje si existe y hay texto
        const messageText = document.querySelector('textarea[name="message"]');
        const messagePrice = parseFloat(document.getElementById('messagePrice')?.value || 0);
        if (messageText && messageText.value.trim() !== '' && messagePrice > 0) {
            basePrice += messagePrice;
        }

        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        const total = basePrice * quantity;

        document.getElementById('totalPrice').innerHTML = 'S/. ' + total.toFixed(2);
    }

    // =============================================
    // TOGGLE DE GRUPOS DE PERSONALIZACIÓN
    // =============================================
    document.querySelectorAll('.group-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const group = this.dataset.group;
            const optionsDiv = document.getElementById('options_' + group);
            const radios = optionsDiv.querySelectorAll('.config-option');

            if (this.checked) {
                optionsDiv.style.display = 'block';
                radios.forEach(r => {
                    r.disabled = false;
                });
            } else {
                optionsDiv.style.display = 'none';
                radios.forEach(r => {
                    r.checked = false;
                    r.disabled = true;
                });
            }
            calculatePrice();
        });
    });

    // =============================================
    // EVENTOS PARA ACTUALIZAR PRECIO AL SELECCIONAR OPCIONES
    // =============================================
    // ✅ SOLUCIÓN: Escuchar clics en los labels (custom-option)
    // que contienen radios o checkboxes
    document.querySelectorAll('.custom-option').forEach(option => {
        option.addEventListener('click', function(e) {
            // Forzar recálculo después de que el navegador actualice el estado del input
            setTimeout(() => {
                calculatePrice();
            }, 50);
        });
    });

    // También mantener los eventos originales para inputs directos
    document.querySelectorAll('.config-option, .addon-checkbox, #quantity, textarea[name="message"]').forEach(el => {
        el.addEventListener('change', calculatePrice);
        el.addEventListener('input', calculatePrice);
    });

    // =============================================
    // BOTONES DE CANTIDAD
    // =============================================
    document.getElementById('decrementQty').addEventListener('click', function() {
        const input = document.getElementById('quantity');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
            setTimeout(calculatePrice, 50);
        }
    });
    document.getElementById('incrementQty').addEventListener('click', function() {
        const input = document.getElementById('quantity');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
        setTimeout(calculatePrice, 50);
    });

    // =============================================
    // INICIALIZAR PRECIO
    // =============================================
    calculatePrice();
</script>
@endpush
