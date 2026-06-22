@extends('layouts.retro')

@section('title', $product->name . ' - Vizio\'s')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Columna Izquierda: Imagen -->
        <div>
            <div class="retro-card p-4">
                <img src="{{ $product->image_url ?? 'https://placehold.co/600x400/000000/FFFFFF?text=' . urlencode($product->name) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-auto">
            </div>
        </div>

        <!-- Columna Derecha: Configuración -->
        <div class="retro-card p-6">
            <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
            <p class="text-gray-600 mb-6">{{ $product->description }}</p>

            <form id="productConfigForm">
                <input type="hidden" id="productId" value="{{ $product->id }}">

                <!-- Tamaño -->
                @if($product->has_sizes && isset($configurations['sizes']) && count($configurations['sizes']) > 0)
                <div class="mb-6">
                    <label class="block font-bold mb-3">📏 TAMAÑO</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($configurations['sizes'] as $size)
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded hover:border-[#ff00ff] cursor-pointer">
                            <input type="radio" name="size" value="{{ $size->id }}" data-price="{{ $size->price_modifier }}"
                                   class="mr-3 config-option" data-type="size">
                            <div>
                                <div class="font-bold">{{ $size->name }}</div>
                                @if($size->price_modifier > 0)
                                <small class="text-[#ff00ff]">+ S/. {{ number_format($size->price_modifier, 2) }}</small>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Número de Pisos -->
                @if($product->has_layers && isset($configurations['layers']) && count($configurations['layers']) > 0)
                <div class="mb-6">
                    <label class="block font-bold mb-3"> NÚMERO DE PISOS</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($configurations['layers'] as $layer)
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded hover:border-[#ff00ff] cursor-pointer">
                            <input type="radio" name="layers" value="{{ $layer->id }}" data-price="{{ $layer->price_modifier }}"
                                   class="mr-3 config-option" data-type="layers">
                            <div>
                                <div class="font-bold">{{ $layer->name }}</div>
                                @if($layer->price_modifier > 0)
                                <small class="text-[#ff00ff]">+ S/. {{ number_format($layer->price_modifier / 100, 2) }}</small>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Sabor -->
                @if($product->has_flavors && isset($configurations['flavors']) && count($configurations['flavors']) > 0)
                <div class="mb-6">
                    <label class="block font-bold mb-3"> SABOR DEL BIZCOCHO</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($configurations['flavors'] as $flavor)
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded hover:border-[#ff00ff] cursor-pointer">
                            <input type="radio" name="flavor" value="{{ $flavor->id }}" data-price="{{ $flavor->price_modifier }}"
                                   class="mr-3 config-option" data-type="flavor">
                            <div>
                                <div class="font-bold">{{ $flavor->name }}</div>
                                @if($flavor->price_modifier > 0)
                                <small class="text-[#ff00ff]">+ S/. {{ number_format($flavor->price_modifier / 100, 2) }}</small>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Relleno -->
                @if($product->has_fillings && isset($configurations['fillings']) && count($configurations['fillings']) > 0)
                <div class="mb-6">
                    <label class="block font-bold mb-3">RELLENO</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($configurations['fillings'] as $filling)
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded hover:border-[#ff00ff] cursor-pointer">
                            <input type="radio" name="filling" value="{{ $filling->id }}" data-price="{{ $filling->price_modifier }}"
                                   class="mr-3 config-option" data-type="filling">
                            <div>
                                <div class="font-bold">{{ $filling->name }}</div>
                                @if($filling->price_modifier > 0)
                                <small class="text-[#ff00ff]">+ S/. {{ number_format($filling->price_modifier / 100, 2) }}</small>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Cobertura -->
                @if($product->has_coverings && isset($configurations['coverings']) && count($configurations['coverings']) > 0)
                <div class="mb-6">
                    <label class="block font-bold mb-3">COBERTURA</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($configurations['coverings'] as $covering)
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded hover:border-[#ff00ff] cursor-pointer">
                            <input type="radio" name="covering" value="{{ $covering->id }}" data-price="{{ $covering->price_modifier }}"
                                   class="mr-3 config-option" data-type="covering">
                            <div>
                                <div class="font-bold">{{ $covering->name }}</div>
                                @if($covering->price_modifier > 0)
                                <small class="text-[#ff00ff]">+ S/. {{ number_format($covering->price_modifier / 100, 2) }}</small>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Mensaje Personalizado -->
                <div class="mb-6">
                    <label class="block font-bold mb-3">MENSAJE ESPECIAL</label>
                    <textarea id="message" rows="2" class="w-full p-3 border-2 border-gray-300"
                              placeholder="Escribe el mensaje que quieres en tu torta..."></textarea>
                </div>

                <!-- Adicionales -->
                <div class="mb-6">
                    <label class="block font-bold mb-3">ADICIONALES</label>
                    <div class="space-y-2">
                        @foreach($addons as $addon)
                        <label class="flex items-center p-2 cursor-pointer">
                            <input type="checkbox" name="addons[]" value="{{ $addon->id }}" class="mr-3 addon-option" data-price="{{ $addon->price }}">
                            <div class="flex-1">
                                <span class="font-bold">{{ $addon->name }}</span>
                                <span class="text-sm text-gray-600 block">{{ $addon->description }}</span>
                            </div>
                            <span class="text-[#ff00ff]">+ S/. {{ number_format($addon->price, 2) }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Cantidad -->
                <div class="mb-6">
                    <label class="block font-bold mb-3">CANTIDAD</label>
                    <input type="number" id="quantity" value="1" min="1" class="w-32 p-2 border-2 border-gray-300 text-center">
                </div>

                <!-- Resumen y Precio -->
                <div class="border-t-2 border-gray-300 pt-6 mb-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-[#ff00ff] mb-4" id="totalPrice">
                           S/. {{ number_format($product->base_price, 2) }}
                        </div>
                        <button type="button" onclick="addToCart()" class="btn-retro-primary w-full py-3">
                            <i class="bi bi-cart-plus"></i> AGREGAR AL CARRITO
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calculatePrice() {
        let basePrice = {{ $product->base_price }};

        // Sumar configuraciones seleccionadas
        document.querySelectorAll('.config-option:checked').forEach(option => {
            basePrice += parseInt(option.dataset.price || 0);
        });

        // Sumar adicionales seleccionados
        document.querySelectorAll('.addon-option:checked').forEach(option => {
            basePrice += parseInt(option.dataset.price || 0);
        });

        const quantity = parseInt(document.getElementById('quantity').value);
        const total = basePrice * quantity;

        document.getElementById('totalPrice').innerHTML = 'S/. ' + (total ).toFixed(2);
    }

    function addToCart() {
        // Recopilar todas las selecciones
        const configurations = [];

        document.querySelectorAll('.config-option:checked').forEach(option => {
            configurations.push(option.value);
        });

        const addons = [];
        document.querySelectorAll('.addon-option:checked').forEach(option => {
            addons.push(option.value);
        });

        const configData = {
            product_id: {{ $product->id }},
            quantity: parseInt(document.getElementById('quantity').value),
            configurations: configurations,
            addons: addons,
            message: document.getElementById('message').value,
            // Guardar nombres legibles para mostrar después
            size: document.querySelector('input[name="size"]:checked')?.closest('label')?.querySelector('.font-bold')?.innerText || null,
            layers: document.querySelector('input[name="layers"]:checked')?.closest('label')?.querySelector('.font-bold')?.innerText || null,
            flavor: document.querySelector('input[name="flavor"]:checked')?.closest('label')?.querySelector('.font-bold')?.innerText || null,
            filling: document.querySelector('input[name="filling"]:checked')?.closest('label')?.querySelector('.font-bold')?.innerText || null,
            covering: document.querySelector('input[name="covering"]:checked')?.closest('label')?.querySelector('.font-bold')?.innerText || null
        };

        // Guardar en localStorage el carrito
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        cart.push(configData);
        localStorage.setItem('cart', JSON.stringify(cart));

        alert('✅ Producto agregado al carrito');
        window.location.href = "{{ route('cart') }}";
    }

    // Event listeners para recalcular precio
    document.querySelectorAll('.config-option, .addon-option, #quantity').forEach(element => {
        element.addEventListener('change', calculatePrice);
        element.addEventListener('input', calculatePrice);
    });

    calculatePrice();
</script>
@endpush
@endsection
