<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Vizio\'s')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="retro-logo">
                    <span class="logo-v">◢</span>
                    <span class="logo-text">VIZIO'S</span>
                    <span class="logo-v">◣</span>
                </div>
                <span class="admin-badge">ADMIN</span>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.products') }}" class="nav-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Productos
                </a>
                <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Pedidos
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Usuarios
                </a>
                <a href="{{ route('admin.payments.pending') }}" class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Pagos Pendientes
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="{{ route('home') }}" class="nav-item">
                    <i class="bi bi-box-arrow-left"></i> Volver al Sitio
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-item w-100 text-start">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-topbar">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-list sidebar-toggle" style="font-size: 1.5rem; cursor: pointer;"></i>
                    <span>Bienvenido, {{ Auth::user()->name }}</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-bell"></i>
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="admin-avatar">
                </div>
            </div>
            
            <div class="admin-content">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>