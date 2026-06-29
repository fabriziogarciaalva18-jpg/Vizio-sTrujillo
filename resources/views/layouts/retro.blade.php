<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vizio\'s · Pastelería Artesanal')</title>
<!-- ============================================ -->
<!-- Favicon tradicional (.ico) -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

<!-- Favicon SVG (navegadores modernos) -->
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

<!-- Favicon PNG (tamaños específicos) -->
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">

<!-- Apple Touch Icon (iOS / Safari) -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

<!-- Web App Manifest (PWA) -->
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <style>
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #0A0A0A;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
        }
        .position-relative {
            position: relative;
        }

        /* Avatar en navbar */
        .nav-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--gray-200);
        }

        .dropdown-menu-retro {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-top: 0.5rem;
        }

        .dropdown-menu-retro .dropdown-item {
            font-size: 0.8rem;
            color: var(--gray-600);
            padding: 0.5rem 1rem;
        }

        .dropdown-menu-retro .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--black);
        }

        .dropdown-menu-retro .dropdown-item i {
            margin-right: 8px;
            width: 18px;
        }

        .dropdown-divider {
            border-top-color: var(--gray-200);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-retro sticky-top">
        <div class="container">
            <!-- LOGO -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/tu-logo.png') }}" alt="Vizio's" height="45">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">INICIO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('catalog') ? 'active' : '' }}" href="{{ route('catalog') }}">CATÁLOGO</a>
                    </li>

                    @auth
                    <!-- CARRITO -->
                    @php
                        $cartCount = 0;
                        $cart = session()->get('cart', []);
                        foreach ($cart as $item) {
                            $cartCount += $item['quantity'];
                        }
                    @endphp
                    <li class="nav-item position-relative">
                        <a class="nav-link {{ request()->routeIs('cart') ? 'active' : '' }}" href="{{ route('cart') }}">
                             CARRITO
                            @if($cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">MIS PEDIDOS</a>
                    </li>

                    <!-- DROPDOWN CON AVATAR -->

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="nav-avatar">
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-retro dropdown-menu-end">
                            @if(auth()->user()->is_admin)
                            <li>
    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-shield-lock-fill"></i> Panel Admin
    </a>
</li>
<li><hr class="dropdown-divider"></li>
@endif
@if(auth()->user()->is_delivery)
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('delivery.*') ? 'active' : '' }}" href="{{ route('delivery.dashboard') }}">
            <i class="bi bi-truck"></i> ENTREGAS
        </a>
    </li>
@endif
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person"></i> Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="bi bi-receipt"></i> Mis Pedidos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('cart') }}">
                                    <i class="bi bi-cart"></i> Mi Carrito
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>

                @guest
                <div class="ms-3 d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn-retro-secondary btn-sm">
                        <i class="bi bi-box-arrow-in-right"></i> INGRESAR
                    </a>
                    <a href="{{ route('register') }}" class="btn-retro-primary btn-sm">
                        <i class="bi bi-person-plus"></i> REGISTRARSE
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-retro" style="background: #DCFCE7; border-color: #86EFAC; color: #166534;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-retro" style="background: #FEE2E2; border-color: #FCA5A5; color: #991B1B;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="container mt-3">
        <div class="alert alert-retro" style="background: #FEF3C7; border-color: #FDE68A; color: #92400E;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('warning') }}
        </div>
    </div>
    @endif

    <!-- CONTENIDO PRINCIPAL -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-brand">
                        <img src="{{ asset('assets/tu-logo.png') }}" alt="Vizio's" height="35">
                        <p class="mt-3">Dulces artesanales hechos con dedicación y el mejor sabor. Desde 2015 endulzando momentos especiales.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4>ENLACES</h4>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-white text-decoration-none">INICIO</a></li>
                        <li><a href="{{ route('catalog') }}" class="text-white text-decoration-none">CATÁLOGO</a></li>
                        @auth
                        <li><a href="{{ route('cart') }}" class="text-white text-decoration-none">CARRITO</a></li>
                        <li><a href="{{ route('orders.index') }}" class="text-white text-decoration-none">MIS PEDIDOS</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-white text-decoration-none">PERFIL</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>CONTACTO</h4>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt"></i> Trujillo, Perú</li>
                        <li><i class="bi bi-envelope"></i> viziostrujillo@gmail.com</li>
                        <li><i class="bi bi-telephone"></i> +51 950 207 553</li>
                    </ul>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/viziostrujillo/reels/"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.facebook.com/viziostrujillo/?locale=es_LA"><i class="bi bi-facebook"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>VIZIO'S © {{ date('Y') }} · PASTELERÍA ARTESANAL · TRUJILLO - PERÚ</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/ui.js') }}"></script>
    @stack('scripts')
</body>
</html>
