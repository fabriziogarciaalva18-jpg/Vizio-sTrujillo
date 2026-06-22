@extends('layouts.retro')

@section('title', 'Catálogo - Vizio\'s')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h1 class="section-title" style="font-size: 1.8rem;">← CATÁLOGO →</h1>
        <div class="section-divider"></div>
    </div>

    <!-- Filtros -->
    <div class="filter-container mb-4">
        <span class="filter-label"><i class="bi bi-funnel"></i> FILTRAR:</span>
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">TODOS</button>
            @foreach($categories as $category)
            <button class="filter-btn" data-filter="{{ $category->slug }}">{{ $category->name }}</button>
            @endforeach
        </div>
    </div>

    <!-- Grid de productos -->
    <div class="product-grid" id="productGrid">
        @forelse($products as $product)
        <div class="product-item" data-category="{{ $product->category->slug ?? 'general' }}">
            <div class="product-card">
                <div class="product-image">
                    <img src="{{ $product->image_url ?? 'https://placehold.co/400x300/F5F3EE/0A0A0A?text=' . urlencode($product->name) }}"
                         alt="{{ $product->name }}"
                         loading="lazy">
                </div>
                <div class="product-body">
                    <h3 class="product-title">{{ $product->name }}</h3>
                    <p class="product-desc">{{ Str::limit($product->description, 80) }}</p>
                    <div class="product-price">S/. {{ number_format($product->base_price, 2) }}</div>

                    <!-- ========================================= -->
                    <!-- BOTONES DE ACCIÓN (con verificación de email) -->
                    <!-- ========================================= -->
                    <div class="product-actions">
                        @auth
                            @if(auth()->user()->hasVerifiedEmail())
                                <!-- Botón VER -->
                                <a href="{{ url('/product/' . $product->id) }}" class="btn-retro-primary btn-action">
                                    <i class="bi bi-eye"></i> VER
                                </a>
                                <!-- Botón AGREGAR como FORMULARIO -->
                                <form action="{{ route('cart.add') }}" method="POST" style="display: inline; flex: 1;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-retro-secondary btn-action" style="width: 100%;">
                                        <i class="bi bi-cart-plus"></i> AGREGAR
                                    </button>
                                </form>
                            @else
                                <!-- Usuario NO VERIFICADO: botón para verificar correo -->
                                <a href="{{ route('verification.notice') }}" class="btn-retro-warning btn-action" style="width: 100%;">
                                    <i class="bi bi-envelope-exclamation"></i> VERIFICAR CORREO
                                </a>
                            @endif
                        @else
                            <!-- Usuario NO AUTENTICADO -->
                            <a href="{{ route('login') }}" class="btn-retro-primary btn-action">
                                <i class="bi bi-box-arrow-in-right"></i> INGRESAR
                            </a>
                            <a href="{{ route('register') }}" class="btn-retro-secondary btn-action">
                                <i class="bi bi-person-plus"></i> REGISTRARSE
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-emoji-frown" style="font-size: 3rem; color: #9E9890;"></i>
            <p class="mt-3 text-muted">No hay productos disponibles en este momento.</p>
            @auth
                <a href="{{ route('admin.products.index') }}" class="btn-retro-primary mt-2">
                    <i class="bi bi-plus-circle"></i> Agregar productos
                </a>
            @endauth
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filtros
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            document.querySelectorAll('.product-item').forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Mostrar notificaciones flash desde la sesión
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.style.cssText = `position: fixed; bottom: 20px; right: 20px; z-index: 9999; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); animation: fadeIn 0.3s ease;`;

        if (type === 'success') {
            notification.style.background = '#DCFCE7';
            notification.style.color = '#166534';
            notification.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + message;
        } else {
            notification.style.background = '#FEE2E2';
            notification.style.color = '#991B1B';
            notification.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + message;
        }

        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // CSS animation para notificaciones
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush