@extends('layouts.retro')

@section('title', 'Vizio\'s · Pastelería Artesanal')

@section('content')
<!-- HERO SECTION -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="bi bi-star-fill"></i> DESDE 2015
                </div>
                <h1 class="hero-title">
                    DULCES <br>
                    <span class="hero-highlight">hechos con precisión</span>
                </h1>
                <p class="hero-desc">
                    Pastelería artesanal que combina tradición y diseño contemporáneo.
                    Cada creación está pensada para momentos inolvidables.
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('catalog') }}" class="btn-retro-primary">
                        <i class="bi bi-grid-3x3-gap-fill"></i> VER CATÁLOGO
                    </a>
                    @guest
                    <a href="{{ route('login') }}" class="btn-retro-secondary">
                        <i class="bi bi-box-arrow-in-right"></i> INGRESAR
                    </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="hero-image-wrapper">
                    <div class="hero-image-border">
                        <img src="{{ asset('assets/tu-logo.png') }}" alt="Vizio's" style="width: 100%; max-width: 300px; display: block; margin: 0 auto;">
                    </div>
                    <div class="hero-decoration"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SOBRE NOSOTROS -->
<section class="about-section">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <span class="section-badge"><i class="bi bi-info-circle"></i> PROPÓSITO</span>
                <h2 class="section-title">← SOBRE VIZIO'S →</h2>
                <div class="section-divider"></div>
                <p class="about-text">
                    Creamos experiencias dulces con ingredientes de alta calidad y un enfoque
                    minimalista que resalta los sabores auténticos. Desde 2015, endulzamos
                    momentos especiales con diseño y precisión.
                </p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="about-card">
                    <i class="bi bi-cup-straw about-icon"></i>
                    <h3>RECETAS TRADICIONALES</h3>
                    <p>Elaboradas con ingredientes de la más alta calidad y recetas perfeccionadas durante generaciones.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="about-card">
                    <i class="bi bi-palette about-icon"></i>
                    <h3>DISEÑO CONTEMPORÁNEO</h3>
                    <p>Estética minimalista que combina la tradición pastelera con líneas limpias y atemporales.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="about-card">
                    <i class="bi bi-heart about-icon"></i>
                    <h3>DEDICACIÓN ARTESANAL</h3>
                    <p>Cada creación es única, elaborada a mano con atención meticulosa al detalle.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCTOS MÁS VENDIDOS -->
<section class="featured-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="bi bi-stars"></i> LO MÁS PEDIDO</span>
            <h2 class="section-title">← SELECCIÓN →</h2>
            <div class="section-divider"></div>
        </div>

        @if($topProducts->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-emoji-frown" style="font-size: 3rem; color: #C4BFB5;"></i>
                <p class="mt-3 text-muted">Aún no hay productos destacados. ¡Vuelve pronto!</p>
                <a href="{{ route('catalog') }}" class="btn-retro-primary mt-2">
                    <i class="bi bi-grid-3x3-gap-fill"></i> VER CATÁLOGO
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach($topProducts as $product)
                <div class="col-md-4">
                    <div class="product-card h-100">
                        <div class="product-image">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                            @if($loop->first)
                                <span class="product-badge">MÁS VENDIDO</span>
                            @endif
                        </div>
                        <div class="product-body d-flex flex-column">
                            <h3 class="product-title">{{ $product->name }}</h3>
                            <p class="product-desc flex-grow-1">{{ Str::limit($product->description, 60) }}</p>
                            <div class="product-price mt-2">S/. {{ number_format($product->base_price, 2) }}</div>
                            <a href="{{ route('products.show', $product) }}" class="btn-retro-primary w-100 mt-2">
                                <i class="bi bi-eye"></i> VER DETALLE
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- TESTIMONIOS -->
<section class="testimonials-section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="bi bi-chat-quote"></i> TESTIMONIOS</span>
            <h2 class="section-title">← CLIENTES →</h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <i class="bi bi-chat-quote testimonial-icon"></i>
                    <p>"El diseño y el sabor superaron mis expectativas. Sin duda volveré a pedir."</p>
                    <div class="testimonial-author">
                        <strong>MARÍA G.</strong>
                        <span>CLIENTE FRECUENTE</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <i class="bi bi-chat-quote testimonial-icon"></i>
                    <p>"Excelente servicio y atención al detalle. La mesa dulce fue un éxito total."</p>
                    <div class="testimonial-author">
                        <strong>CARLOS R.</strong>
                        <span>EVENTO CORPORATIVO</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <i class="bi bi-chat-quote testimonial-icon"></i>
                    <p>"Los alfajores son deliciosos y la presentación impecable. 10/10."</p>
                    <div class="testimonial-author">
                        <strong>LAURA M.</strong>
                        <span>RECOMENDADO</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
