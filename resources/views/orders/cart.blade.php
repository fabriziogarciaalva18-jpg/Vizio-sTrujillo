@extends('layouts.retro')

@section('title', 'Mi Carrito - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← MI CARRITO →</h1>
        <div class="section-divider"></div>
    </div>

    @php
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $price = $item['unit_price'] ?? $item['price'] ?? 0;
            $total += $price * $item['quantity'];
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
        <div class="col-lg-8">
            @foreach($cart as $key => $item)
            <div class="order-card mb-3">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="mb-0">{{ $item['name'] }}</h4>
                        @if(!empty($item['message']))
                            <span class="badge bg-light text-dark"><i class="bi bi-chat-text"></i> "{{ $item['message'] }}"</span>
                        @endif
                        @if(!empty($item['selected_configs']))
                            <div class="small text-muted mt-1">
                                @php
                                    $configNames = [];
                                    foreach($item['selected_configs'] as $type => $configId) {
                                        $config = App\Models\ProductConfiguration::find($configId);
                                        if($config) {
                                            $configNames[] = ucfirst($type) . ': ' . $config->name;
                                        }
                                    }
                                @endphp
                                {{ implode(' | ', $configNames) }}
                            </div>
                        @endif
                        @if(!empty($item['selected_addons']))
                            <div class="small text-muted">
                                <i class="bi bi-plus-circle"></i> {{ count($item['selected_addons']) }} adicional(es)
                            </div>
                        @endif
                        <div class="small text-muted">
                            <i class="bi bi-currency-dollar"></i> Precio unitario: S/. {{ number_format($item['unit_price'] ?? $item['price'] ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <button class="btn-retro-secondary btn-sm decrement" data-key="{{ $key }}">-</button>
                            <input type="number" class="form-control form-control-sm text-center cart-qty"
                                   data-key="{{ $key }}" value="{{ $item['quantity'] }}" min="1"
                                   style="width: 60px; margin: 0 5px;">
                            <button class="btn-retro-secondary btn-sm increment" data-key="{{ $key }}">+</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <span class="item-total">S/. {{ number_format(($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'], 2) }}</span>
                    </div>
                    <div class="col-md-2 text-end">
                        <!-- Botón Editar -->
                        <button class="btn-retro-secondary btn-sm me-1 edit-item" data-key="{{ $key }}" data-product="{{ $item['id'] }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <!-- Botón Detalles -->
                        <button class="btn-retro-secondary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#detailsModal-{{ $key }}">
                            <i class="bi bi-eye"></i>
                        </button>
                        <!-- Botón Eliminar -->
                        <button class="btn-retro-danger btn-sm remove-item" data-key="{{ $key }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========================================= -->
            <!-- MODAL DE DETALLES (igual que antes)       -->
            <!-- ========================================= -->
            <div class="modal fade" id="detailsModal-{{ $key }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content modal-retro">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-box-seam"></i> Detalles de "{{ $item['name'] }}"
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Igual que antes: muestra configs, addons, mensaje, precios -->
                            <p><strong>Producto:</strong> {{ $item['name'] }}</p>
                            <hr>
                            @if(!empty($item['selected_configs']))
                                <h6><i class="bi bi-sliders2"></i> Personalizaciones</h6>
                                <ul class="list-unstyled">
                                @foreach($item['selected_configs'] as $type => $configId)
                                    @php
                                        $config = App\Models\ProductConfiguration::find($configId);
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
                                    <li><strong>{{ $label }}:</strong> {{ $config->name ?? $configId }}</li>
                                @endforeach
                                </ul>
                                <hr>
                            @endif
                            @if(!empty($item['selected_addons']))
                                <h6><i class="bi bi-plus-circle"></i> Adicionales</h6>
                                <ul>
                                @foreach($item['selected_addons'] as $addonId)
                                    @php
                                        $addon = App\Models\Addon::find($addonId);
                                    @endphp
                                    <li>{{ $addon->name ?? $addonId }} (S/. {{ number_format($addon->price ?? 0, 2) }})</li>
                                @endforeach
                                </ul>
                                <hr>
                            @endif
                            @if(!empty($item['message']))
                                <p><strong><i class="bi bi-chat-text"></i> Mensaje:</strong> "{{ $item['message'] }}"</p>
                                <hr>
                            @endif
                            <div class="row">
                                <div class="col-6"><strong>Precio unitario:</strong> S/. {{ number_format($item['unit_price'] ?? $item['price'] ?? 0, 2) }}</div>
                                <div class="col-6"><strong>Cantidad:</strong> {{ $item['quantity'] }}</div>
                            </div>
                            <div class="mt-2"><strong>Subtotal:</strong> S/. {{ number_format(($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'], 2) }}</div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-retro-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-lg-4">
            <div class="profile-card">
                <h3 class="profile-section-title">RESUMEN</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>S/. {{ number_format($total, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Envío</span>
                    <span>S/. 8.00</span>
                </div>
                <hr>
                <div class="summary-row total">
                    <span><strong>TOTAL</strong></span>
                    <span><strong>S/. {{ number_format($total + 8.00, 2) }}</strong></span>
                </div>
                <a href="{{ route('checkout') }}" class="btn-retro-primary w-100 mt-3">
                    <i class="bi bi-credit-card"></i> PROCEDER AL PAGO
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- ========================================= -->
<!-- MODAL DE EDICIÓN DE PERSONALIZACIONES     -->
<!-- ========================================= -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-retro">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar personalizaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- Contenido cargado dinámicamente con JavaScript -->
                <div class="text-center py-4">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-retro-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-retro-primary" id="saveEditBtn">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // =============================================
    // FUNCIONES EXISTENTES (actualizar, eliminar, etc.)
    // =============================================
    function updateCart() {
        location.reload();
    }

    document.querySelectorAll('.cart-qty').forEach(input => {
        input.addEventListener('change', function() {
            const key = this.dataset.key;
            const quantity = this.value;
            fetch(`/cart/update/${key}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) updateCart();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la cantidad');
            });
        });
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.dataset.key;
            if (confirm('¿Eliminar este producto del carrito?')) {
                fetch(`/cart/remove/${key}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error');
                    return response.json();
                })
                .then(data => {
                    if (data.success) updateCart();
                    else alert(data.message || 'Error');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto. Intenta nuevamente.');
                });
            }
        });
    });

    document.querySelectorAll('.decrement').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.dataset.key;
            const input = document.querySelector(`.cart-qty[data-key="${key}"]`);
            if (input && parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    document.querySelectorAll('.increment').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.dataset.key;
            const input = document.querySelector(`.cart-qty[data-key="${key}"]`);
            if (input) {
                input.value = parseInt(input.value) + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    // =============================================
    // EDITAR PERSONALIZACIONES
    // =============================================
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    let currentEditKey = null;

    document.querySelectorAll('.edit-item').forEach(btn => {
        btn.addEventListener('click', function() {
            currentEditKey = this.dataset.key;
            const productId = this.dataset.product;
            const modalBody = document.getElementById('editModalBody');
            // Mostrar spinner
            modalBody.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Cargando...</span></div></div>`;
            editModal.show();

            // Cargar datos del producto y personalizaciones
            fetch(`/api/product/${productId}/customizations`)
                .then(response => response.json())
                .then(data => {
                    renderEditForm(data, currentEditKey);
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `<div class="alert alert-danger">Error al cargar las opciones. Intenta nuevamente.</div>`;
                });
        });
    });

function renderEditForm(data, key) {
    const modalBody = document.getElementById('editModalBody');
    const cart = @json($cart);
    const currentItem = cart[key];

    let html = `<form id="editForm">`;
    html += `<input type="hidden" name="cart_key" value="${key}">`;

    // Producto y precio base
    html += `<p><strong>Producto:</strong> ${data.product.name}</p>`;

    // ===== CONFIGURACIONES =====
    const configs = data.configurations;
    for (const [type, items] of Object.entries(configs)) {
        const labelMap = {
            'size': 'Tamaño',
            'layers': 'Pisos',
            'flavor': 'Sabor',
            'filling': 'Relleno',
            'covering': 'Cobertura',
            'shape': 'Forma',
            'color': 'Color',
            'toppings': 'Toppings',
            'decoration': 'Decoración'
        };
        const label = labelMap[type] || type;
        html += `<div class="mb-3"><label class="form-label fw-bold">${label}</label><div class="d-flex flex-wrap gap-2">`;
        const currentSelected = currentItem.selected_configs[type] ?? null;
        items.forEach(item => {
            const checked = (item.id == currentSelected) ? 'checked' : '';
            const price = parseFloat(item.price_modifier) || 0; // ✅ Convertir a número
            html += `
                <label class="custom-option">
                    <input type="radio" name="configurations[${type}]" value="${item.id}" data-price="${price}" ${checked}>
                    <span class="option-label">
                        ${item.name}
                        ${price > 0 ? `<span class="badge-price">+ S/. ${price.toFixed(2)}</span>` : ''}
                    </span>
                </label>
            `;
        });
        html += `</div></div>`;
    }

    // ===== MENSAJE PERSONALIZADO =====
    if (data.message_config) {
        const msg = data.message_config;
        const msgPrice = parseFloat(msg.price_modifier) || 0;
        html += `
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="bi bi-chat-text"></i> Mensaje personalizado ${msgPrice > 0 ? `(+ S/. ${msgPrice.toFixed(2)})` : ''}</label>
                <textarea name="message" class="form-control form-control-retro" rows="2" placeholder="Escribe el mensaje...">${currentItem.message || ''}</textarea>
                <input type="hidden" name="message_price" value="${msgPrice}">
            </div>
        `;
    }

    // ===== ADICIONALES =====
    if (data.addons && data.addons.length > 0) {
        html += `<div class="mb-3"><label class="form-label fw-bold"><i class="bi bi-plus-circle"></i> Adicionales</label><div class="row g-2">`;
        const currentAddons = currentItem.selected_addons || [];
        data.addons.forEach(addon => {
            const checked = currentAddons.includes(addon.id) ? 'checked' : '';
            const price = parseFloat(addon.price) || 0; // ✅ Convertir a número
            html += `
                <div class="col-12 col-md-6">
                    <label class="custom-option addon-option">
                        <input type="checkbox" name="addons[]" value="${addon.id}" data-price="${price}" ${checked}>
                        <span class="option-label">
                            ${addon.name}
                            <small class="text-muted d-block">${addon.description || ''}</small>
                            <span class="badge-price">+ S/. ${price.toFixed(2)}</span>
                        </span>
                    </label>
                </div>
            `;
        });
        html += `</div></div>`;
    }

    // ===== CANTIDAD =====
    html += `
        <div class="mb-3">
            <label class="form-label fw-bold"><i class="bi bi-hash"></i> Cantidad</label>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn-retro-secondary btn-sm edit-decrement">−</button>
                <input type="number" name="quantity" id="editQuantity" value="${currentItem.quantity}" min="1" class="form-control form-control-retro text-center" style="width: 80px;">
                <button type="button" class="btn-retro-secondary btn-sm edit-increment">+</button>
            </div>
        </div>
    `;

    // ===== TOTAL PROVISIONAL =====
    const unitPrice = parseFloat(currentItem.unit_price) || 0;
    const qty = parseInt(currentItem.quantity) || 1;
    html += `
        <hr>
        <div class="d-flex justify-content-between">
            <span class="fw-bold">Total con extras</span>
            <span class="fw-bold" id="editTotalPrice">S/. ${(unitPrice * qty).toFixed(2)}</span>
        </div>
    `;

    html += `</form>`;
    modalBody.innerHTML = html;

    // Añadir eventos de precio dinámico
    attachEditPriceListeners(data.product.base_price);
}

    function attachEditPriceListeners(basePrice) {
        const form = document.getElementById('editForm');
        if (!form) return;

        function calculateEditPrice() {
            let price = basePrice;
            // Radio configs
            form.querySelectorAll('input[type="radio"]:checked').forEach(el => {
                price += parseFloat(el.dataset.price || 0);
            });
            // Checkbox addons
            form.querySelectorAll('input[type="checkbox"]:checked').forEach(el => {
                price += parseFloat(el.dataset.price || 0);
            });
            // Mensaje
            const msgText = form.querySelector('textarea[name="message"]');
            const msgPrice = parseFloat(form.querySelector('input[name="message_price"]')?.value || 0);
            if (msgText && msgText.value.trim() !== '' && msgPrice > 0) {
                price += msgPrice;
            }
            // Cantidad
            const qty = parseInt(form.querySelector('input[name="quantity"]').value) || 1;
            const total = price * qty;
            document.getElementById('editTotalPrice').innerHTML = 'S/. ' + total.toFixed(2);
            return { unitPrice: price, quantity: qty, total };
        }

        form.querySelectorAll('input, textarea').forEach(el => {
            el.addEventListener('change', calculateEditPrice);
            el.addEventListener('input', calculateEditPrice);
        });

        // Botones cantidad
        form.querySelector('.edit-decrement')?.addEventListener('click', function() {
            const input = form.querySelector('input[name="quantity"]');
            let val = parseInt(input.value) || 1;
            if (val > 1) {
                input.value = val - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        form.querySelector('.edit-increment')?.addEventListener('click', function() {
            const input = form.querySelector('input[name="quantity"]');
            let val = parseInt(input.value) || 1;
            input.value = val + 1;
            input.dispatchEvent(new Event('change'));
        });

        calculateEditPrice();
    }

    // =============================================
    // GUARDAR EDICIÓN
    // =============================================
    document.getElementById('saveEditBtn').addEventListener('click', function() {
        const form = document.getElementById('editForm');
        if (!form) return;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Convertir arrays para addons (ya que FormData envía múltiples)
        const addons = formData.getAll('addons[]');
        data['addons'] = addons;

        // Reconstruir configuraciones
        const configs = {};
        formData.forEach((value, key) => {
            if (key.startsWith('configurations[')) {
                const match = key.match(/configurations\[(.*?)\]/);
                if (match) {
                    configs[match[1]] = value;
                }
            }
        });
        data['configurations'] = configs;

        // Enviar al servidor para actualizar el item
        const key = data.cart_key;
        fetch(`/cart/update-item/${key}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                configurations: configs,
                addons: addons,
                message: data.message || '',
                quantity: parseInt(data.quantity) || 1,
                message_price: parseFloat(data.message_price) || 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                editModal.hide();
                updateCart();
            } else {
                alert(data.message || 'Error al actualizar el carrito');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar los cambios. Intenta nuevamente.');
        });
    });
</script>
@endpush
