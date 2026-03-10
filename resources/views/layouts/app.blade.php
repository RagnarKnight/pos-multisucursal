<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111314">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Mi Negocio">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>@yield('title', auth()->check() && auth()->user()->tiendaActiva() ? auth()->user()->tiendaActiva()->nombre : 'Mi Negocio')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Fonts: Barlow Condensed (display) + Barlow (body) --}}
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --c-bg:        #111314;
            --c-surface:   #1c1f21;
            --c-border:    #2e3235;
            --c-accent:    #f5a623;
            --c-accent-dk: #c4841a;
            --c-text:      #eaeaea;
            --c-muted:     #7a8085;
            --c-danger:    #e84040;
            --c-success:   #2ecc71;
            --nav-h:       60px;
            --font-display: 'Barlow Condensed', sans-serif;
            --font-body:    'Barlow', sans-serif;
        }

        /* ── Modo claro ──────────────────────────────────── */
        body.light-mode {
            --c-bg:      #f0f2f5;
            --c-surface: #ffffff;
            --c-border:  #d8dde3;
            --c-text:    #1a1d20;
            --c-muted:   #6b7280;
        }
        body.light-mode .pos-navbar   { background: #ffffff; }
        body.light-mode .sidebar-offcanvas { background: #ffffff !important; }

        * { transition: background-color 0.2s, color 0.15s, border-color 0.15s; }

        /* ── Botón toggle modo ───────────────────────────── */
        .theme-btn {
            background: none; border: none; color: var(--c-text);
            font-size: 1.2rem; padding: 0.4rem; cursor: pointer;
            border-radius: 8px; transition: background 0.15s, color 0.15s;
            line-height: 1;
        }
        .theme-btn:hover { background: var(--c-border); color: var(--c-accent); }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            background: var(--c-bg);
            color: var(--c-text);
            font-family: var(--font-body);
            font-size: 16px;
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior: none;
        }

        /* ── Navbar ──────────────────────────────────────────── */
        .pos-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--nav-h);
            background: var(--c-surface);
            border-bottom: 2px solid var(--c-accent);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 1000;
        }

        .pos-navbar .brand {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--c-accent);
            letter-spacing: 0.04em;
            text-decoration: none;
        }

        .pos-navbar .brand span {
            color: var(--c-text);
            font-weight: 600;
        }

        .nav-icon-btn {
            background: none;
            border: none;
            color: var(--c-text);
            font-size: 1.5rem;
            padding: 0.4rem;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.15s, color 0.15s;
            line-height: 1;
        }
        .nav-icon-btn:hover, .nav-icon-btn:active {
            background: var(--c-border);
            color: var(--c-accent);
        }

        .nav-user-badge {
            font-size: 0.75rem;
            color: var(--c-muted);
            margin-right: 0.5rem;
            text-align: right;
            line-height: 1.2;
        }
        .nav-user-badge strong { color: var(--c-text); font-size: 0.85rem; }

        /* ── Offcanvas Sidebar ───────────────────────────────── */
        .sidebar-offcanvas {
            background: var(--c-surface) !important;
            border-right: 2px solid var(--c-accent) !important;
            width: 280px !important;
        }

        .sidebar-offcanvas .offcanvas-header {
            border-bottom: 1px solid var(--c-border);
            padding: 1.25rem 1.25rem;
        }

        .sidebar-brand {
            font-family: var(--font-display);
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--c-accent);
        }

        .sidebar-brand span { color: var(--c-text); font-weight: 600; }

        .btn-close-sidebar {
            background: none;
            border: none;
            color: var(--c-muted);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 6px;
            transition: color 0.15s;
        }
        .btn-close-sidebar:hover { color: var(--c-danger); }

        .sidebar-nav { padding: 1rem 0.75rem; }

        .sidebar-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--c-muted);
            padding: 0.5rem 0.5rem 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            color: var(--c-text);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 2px;
            transition: background 0.15s, color 0.15s;
        }
        .sidebar-link i { font-size: 1.2rem; color: var(--c-muted); transition: color 0.15s; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(245,166,35,0.12);
            color: var(--c-accent);
        }
        .sidebar-link:hover i, .sidebar-link.active i { color: var(--c-accent); }

        .sidebar-divider {
            border-color: var(--c-border);
            margin: 0.75rem 0;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--c-border);
        }

        /* ── Contenido principal ─────────────────────────────── */
        .main-content {
            margin-top: var(--nav-h);
            min-height: calc(100vh - var(--nav-h));
            padding: 1rem;
        }

        /* ── Alertas flash ───────────────────────────────────── */
        .flash-container {
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: calc(100% - 2rem);
            max-width: 480px;
        }

        .flash-alert {
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Botones globales ────────────────────────────────── */
        .btn-accent {
            background: var(--c-accent);
            color: #111;
            border: none;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.03em;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-accent:hover   { background: var(--c-accent-dk); color: #111; }
        .btn-accent:active  { transform: scale(0.97); }

        .btn-outline-ghost {
            background: none;
            border: 1.5px solid var(--c-border);
            color: var(--c-text);
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            transition: border-color 0.15s, color 0.15s;
        }
        .btn-outline-ghost:hover { border-color: var(--c-accent); color: var(--c-accent); }

        /* ── Cards ───────────────────────────────────────────── */
        .pos-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 14px;
        }

        /* ── Badges de método de pago ────────────────────────── */
        .badge-efectivo     { background: #1a4731; color: #2ecc71; }
        .badge-transferencia{ background: #1a2e47; color: #3498db; }
        .badge-fiado        { background: #3d1f1f; color: #e84040; }

        @yield('extra-css')
    </style>

    @yield('head')
</head>
<body>

{{-- ── Navbar ──────────────────────────────────────────────── --}}
<nav class="pos-navbar">
    <button class="nav-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Menú">
        <i class="bi bi-list"></i>
    </button>

    @php $tiendaActiva = auth()->user()->tiendaActiva(); @endphp

    {{-- Marca dinámica --}}
    <a href="{{ route('pos.index') }}" class="brand" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;">
        @if($tiendaActiva?->logoUrl())
            <img src="{{ $tiendaActiva->logoUrl() }}" alt="{{ $tiendaActiva->nombre }}"
                 style="height:30px;max-width:110px;object-fit:contain;border-radius:4px;">
        @else
            <span style="font-family:var(--font-display);font-size:1.2rem;font-weight:800;color:var(--c-text);">
                {{ $tiendaActiva?->nombre ?? 'Mi Negocio' }}
            </span>
            @if($tiendaActiva?->ciudad)
                <span style="font-size:0.75rem;color:var(--c-muted);font-weight:400;">
                    {{ $tiendaActiva->ciudad }}
                </span>
            @endif
        @endif
    </a>

    <div class="d-flex align-items-center gap-2">

        {{-- Selector de tienda para superadmin --}}
        @if(auth()->user()->esSuperAdmin())
        <form method="POST" action="{{ route('tienda.switch') }}" style="margin:0;">
            @csrf
            <select name="tienda_id" onchange="this.form.submit()"
                    style="background:var(--c-border);border:1px solid var(--c-border);color:var(--c-text);
                           border-radius:8px;padding:0.3rem 0.5rem;font-size:0.8rem;outline:none;cursor:pointer;
                           max-width:140px;">
                @foreach(\App\Models\Tienda::where('activa',true)->get() as $t)
                    <option value="{{ $t->id }}" {{ $tiendaActiva?->id == $t->id ? 'selected' : '' }}>
                        🏪 {{ $t->nombre }}
                    </option>
                @endforeach
            </select>
        </form>
        @endif

        <div class="nav-user-badge d-none d-sm-block">
            <strong>{{ auth()->user()->name }}</strong><br>
            {{ ucfirst(auth()->user()->rol) }}
        </div>
        <button class="theme-btn" id="themeToggle" title="Cambiar modo oscuro/claro" onclick="toggleTheme()">
            <i class="bi bi-sun-fill" id="themeIcon"></i>
        </button>
        <a href="{{ route('pos.index') }}" class="nav-icon-btn" title="POS">
            <i class="bi bi-grid-3x3-gap"></i>
        </a>
    </div>
</nav>

{{-- ── Sidebar Offcanvas ────────────────────────────────────── --}}
<div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1" id="sidebar">
    <div class="offcanvas-header">
        <span class="sidebar-brand">
            {{ $tiendaActiva?->nombre ?? 'Mi Negocio' }}
            @if($tiendaActiva?->ciudad)
                <span style="font-size:0.8em;opacity:0.6;"> — {{ $tiendaActiva->ciudad }}</span>
            @endif
        </span>
        <button class="btn-close-sidebar" data-bs-dismiss="offcanvas">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column">
        <nav class="sidebar-nav flex-grow-1">

            @if(auth()->user()->esSuperAdmin())
            <div class="sidebar-label">Sistema</div>
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            @endif

            <div class="sidebar-label">Venta</div>
            <a href="{{ route('pos.index') }}"
               class="sidebar-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap-fill"></i> Punto de Venta
            </a>

            <div class="sidebar-label mt-2">Gestión</div>
            <a href="{{ route('customers.index') }}"
               class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> La Libreta
            </a>
            <a href="{{ route('orders.index') }}"
               class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Historial
            </a>
            <a href="{{ route('cajas.index') }}"
               class="sidebar-link {{ request()->routeIs('cajas.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Control de Caja
            </a>
            <a href="{{ route('reports.index') }}"
               class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> Reportes
            </a>

            @if(auth()->user()->esAdmin())
            <hr class="sidebar-divider">
            <div class="sidebar-label">Admin</div>
            <a href="{{ route('products.index') }}"
               class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Productos
            </a>
            <a href="{{ route('users.index') }}"
               class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Usuarios
            </a>
            {{-- Configuración de la tienda --}}
            <a href="{{ route('tiendas.edit', $tiendaActiva) }}"
               class="sidebar-link {{ request()->routeIs('tiendas.edit') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Mi Tienda
            </a>
            @endif

            {{-- Solo superadmin: gestión de tiendas si el flag está activo --}}
            @can('gestionar-tiendas')
            <hr class="sidebar-divider">
            <div class="sidebar-label">Super Admin</div>
            <a href="{{ route('tiendas.index') }}"
               class="sidebar-link {{ request()->routeIs('tiendas.index') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Tiendas
            </a>
            @endcan

        </nav>

        <div class="sidebar-footer">
            <div class="mb-2" style="font-size:0.8rem; color:var(--c-muted);">
                Conectado como<br>
                <strong style="color:var(--c-text);">{{ auth()->user()->name }}</strong>
                — {{ ucfirst(auth()->user()->rol) }}
                @if($tiendaActiva)
                    <br><span style="color:var(--c-accent);">🏪 {{ $tiendaActiva->nombre }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-outline-ghost w-100 text-start d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── Contenido ─────────────────────────────────────────────── --}}
<main class="main-content">
    @yield('content')
</main>

{{-- ── Flash messages ───────────────────────────────────────── --}}
<div class="flash-container">
    @if(session('success'))
        <div class="alert flash-alert alert-success alert-dismissible fade show" role="alert"
             style="background:#1a4731; color:#2ecc71; border-left: 4px solid #2ecc71;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert flash-alert alert-danger alert-dismissible fade show" role="alert"
             style="background:#3d1f1f; color:#e84040; border-left: 4px solid #e84040;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Auto-cerrar flash después de 3s --}}
<script>
    setTimeout(() => {
        document.querySelectorAll('.flash-alert').forEach(el => {
            bootstrap.Alert.getOrCreateInstance(el).close();
        });
    }, 3000);
</script>

@yield('scripts')
<script>
// ── Toggle modo oscuro / claro ────────────────────────────────
function toggleTheme() {
    const body = document.body;
    const icon = document.getElementById('themeIcon');
    const isLight = body.classList.toggle('light-mode');
    icon.className = isLight ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    localStorage.setItem('pos-theme', isLight ? 'light' : 'dark');
}

// Restaurar preferencia guardada
(function() {
    const saved = localStorage.getItem('pos-theme');
    if (saved === 'light') {
        document.body.classList.add('light-mode');
        const icon = document.getElementById('themeIcon');
        if (icon) icon.className = 'bi bi-moon-fill';
    }
})();
</script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
</body>
</html>
