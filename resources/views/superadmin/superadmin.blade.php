<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SuperAdmin — Centro Médico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sa-dark:   #0a1628;
            --sa-navy:   #0d2240;
            --sa-blue:   #0d3b6e;
            --sa-gold:   #d4a017;
            --sa-gold2:  #f0c040;
            --sa-light:  #f4f6fa;
            --sa-border: #1e3a5f;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--sa-light);
            color: #1a1a2e;
        }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px;
            height: 100vh;
            background: var(--sa-dark);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            border-right: 1px solid var(--sa-border);
            transition: transform .3s;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 22px 20px 16px;
            border-bottom: 1px solid var(--sa-border);
        }
        .sidebar-brand .crown {
            background: var(--sa-gold);
            color: #000;
            border-radius: 8px;
            width: 36px; height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .sidebar-brand h6 {
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            margin: 0;
            letter-spacing: .5px;
        }
        .sidebar-brand small {
            color: var(--sa-gold);
            font-size: 0.7rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-section {
            padding: 12px 20px 4px;
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #4a6080;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #8aa0bc;
            text-decoration: none;
            font-size: 0.875rem;
            border-left: 3px solid transparent;
            transition: all .2s;
            position: relative;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            color: #fff;
            background: rgba(212, 160, 23, 0.08);
            border-left-color: var(--sa-gold);
        }
        .sidebar-nav a i { font-size: 1rem; }

        /* Badge de notificación en el sidebar */
        .sidebar-nav a .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
            line-height: 1.4;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid var(--sa-border);
        }
        .sidebar-footer .user-info small { color: #4a6080; font-size: 0.72rem; }
        .sidebar-footer .user-info span { color: #fff; font-weight: 600; font-size: 0.82rem; display: block; }

        /* ── MAIN ── */
        #main {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar .page-title { font-weight: 700; font-size: 1rem; color: var(--sa-dark); }
        .topbar .breadcrumb { font-size: 0.78rem; margin: 0; }

        /* ── BADGE SUPERADMIN ── */
        .sa-badge {
            background: linear-gradient(135deg, var(--sa-gold), var(--sa-gold2));
            color: #000;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* ── CONTENT ── */
        .content-area { padding: 28px; flex: 1; }

        /* ── CARDS ── */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            border: 1px solid #e8edf3;
            transition: box-shadow .2s;
        }
        .stat-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); }
        .stat-card .icon-wrap {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .stat-card .stat-value { font-size: 2rem; font-weight: 800; color: var(--sa-dark); line-height: 1; }
        .stat-card .stat-label { color: #64748b; font-size: 0.8rem; margin-top: 4px; }

        /* ── TABLE ── */
        .sa-table { border-radius: 12px; overflow: hidden; }
        .sa-table thead th {
            background: var(--sa-dark);
            color: #8aa0bc;
            font-size: 0.7rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: none;
            padding: 14px 16px;
        }
        .sa-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .sa-table tbody tr:hover { background: #f8fafc; }
        .sa-table tbody td { padding: 13px 16px; font-size: 0.875rem; vertical-align: middle; border: none; }

        /* ── PAGINACIÓN GLOBAL ── */
        .sa-pagination {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .sa-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
            color: #64748b;
            background: #fff;
            border: 1px solid #e2e8f0;
            transition: all .15s;
            cursor: pointer;
            line-height: 1;
        }
        .sa-page-btn:hover:not(.disabled):not(.active) {
            background: #f1f5f9;
            color: var(--sa-dark);
            border-color: #cbd5e1;
        }
        .sa-page-btn.active {
            background: var(--sa-blue);
            color: #fff;
            border-color: var(--sa-blue);
        }
        .sa-page-btn.disabled {
            opacity: .4;
            cursor: default;
            pointer-events: none;
        }
        .sa-page-btn i,
        .sa-page-btn .bi {
            font-size: .8rem;
            line-height: 1;
        }

        /* ── TOASTS ── */
        #toastContainer { position: fixed; top: 16px; right: 16px; z-index: 9999; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #main { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside id="sidebar">
    <div class="sidebar-brand d-flex align-items-center gap-3">
        <div class="crown"><i class="bi bi-gem"></i></div>
        <div>
            <h6>Centro Médico</h6>
            <small>Super Admin</small>
        </div>
    </div>

    <div class="sidebar-section">General</div>
    <nav class="sidebar-nav">
        <a href="{{ route('superadmin.dashboard') }}"
           class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
    </nav>

    <div class="sidebar-section">Gestión</div>
    <nav class="sidebar-nav">
        <a href="{{ route('superadmin.centros.index') }}"
           class="{{ request()->routeIs('superadmin.centros.*') ? 'active' : '' }}">
            <i class="bi bi-building-fill"></i> Centros Médicos
        </a>
        <a href="{{ route('superadmin.admins.index') }}"
           class="{{ request()->routeIs('superadmin.admins.*') ? 'active' : '' }}">
            <i class="bi bi-shield-fill-check"></i> Administradores
        </a>
        {{-- ── ENLACE SOLICITUDES DE CAMBIO ── --}}
        <a href="{{ route('superadmin.solicitudes.index') }}"
           class="{{ request()->routeIs('superadmin.solicitudes.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i> Cambios de Centro
            @php
                $pendientesSolicitudes = \App\Models\SolicitudCambioCentro::where('estado', 'pendiente')->count();
            @endphp
            @if($pendientesSolicitudes > 0)
                <span class="nav-badge">{{ $pendientesSolicitudes }}</span>
            @endif
        </a>
    </nav>

    <div class="sidebar-section">Usuarios</div>
    <nav class="sidebar-nav">
        <a href="{{ route('superadmin.medicos.index') }}"
           class="{{ request()->routeIs('superadmin.medicos.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i> Médicos
        </a>
        <a href="{{ route('superadmin.pacientes.index') }}"
           class="{{ request()->routeIs('superadmin.pacientes.*') ? 'active' : '' }}">
            <i class="bi bi-person-heart"></i> Pacientes
        </a>
        <a href="{{ route('superadmin.usuarios.citas') }}"
           class="{{ request()->routeIs('superadmin.usuarios.citas') ? 'active' : '' }}">
            <i class="bi bi-calendar-week-fill"></i> Citas
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info mb-3">
            <small>Sesión activa como</small>
            <span>{{ session('superadmin_nombre') }}</span>
            <span class="sa-badge mt-1 d-inline-block">SuperAdmin</span>
        </div>
        <a href="{{ route('superadmin.logout') }}"
           class="btn btn-sm w-100"
           style="background: rgba(212,160,23,.15); color: var(--sa-gold); border: 1px solid rgba(212,160,23,.3); border-radius: 8px;">
            <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
        </a>
    </div>
</aside>

<!-- MAIN -->
<div id="main">
    <!-- TOPBAR -->
    <div class="topbar d-flex align-items-center justify-content-between">
        <div>
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('superadmin.dashboard') }}" class="text-decoration-none" style="color:#64748b;">Inicio</a>
                    </li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="sa-badge"><i class="bi bi-gem me-1"></i>SuperAdmin</span>
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content-area">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- TOAST CONTAINER -->
<div id="toastContainer"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
function saToast(msg, tipo = 'success') {
    const colores = { success:'#1a7a4a', danger:'#dc3545', warning:'#e6a817', info:'#0d3b6e' };
    const iconos  = { success:'bi-check-circle-fill', danger:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };
    const id = 'toast_' + Date.now();
    $('#toastContainer').append(`
        <div id="${id}" class="toast align-items-center text-white border-0 mb-2 show"
            style="background:${colores[tipo]||colores.info}; border-radius:10px; min-width:280px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${iconos[tipo]||iconos.info} fs-5"></i>
                    <span>${msg}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    onclick="document.getElementById('${id}').remove()"></button>
            </div>
        </div>`);
    setTimeout(() => $('#'+id).remove(), 4500);
}

document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>