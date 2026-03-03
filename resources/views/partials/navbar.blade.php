<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm px-4 mb-4">
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">
        {{ config('app.name', 'Laravel') }}
    </a>

    <div class="ms-auto d-flex align-items-center gap-3">

        {{-- ── ADMIN ─────────────────────────────────────────── --}}
        @if(session('admin') === 1)
            <a class="nav-link" href="/citas">📅 Citas</a>
            <a class="nav-link" href="/Especialidad">🩺 Especialidades</a>
            <a class="nav-link" href="/Horario">🕐 Horarios</a>
            <a class="nav-link" href="/Informacion">📋 Informes</a>
            <a class="nav-link" href="/C_usuario">👤 Crear usuario</a>
            <a href="/logout" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>

        {{-- ── MÉDICO ────────────────────────────────────────── --}}
        @elseif(session('cargo') === 'Medico')
            <a class="nav-link" href="/citas">📅 Mis citas</a>
            <a class="nav-link" href="/Informacion">📋 Mis informes</a>
            <a class="nav-link" href="/Horario">🕐 Mi horario</a>
            <a href="/logout" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>

        {{-- ── PACIENTE ──────────────────────────────────────── --}}
        @elseif(session('cargo') === 'Paciente')
            <a class="nav-link" href="/citas">📅 Mis citas</a>
            <a class="nav-link" href="/Informe">📄 Mis informes</a>
            <a href="/logout" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>

        {{-- ── SIN SESIÓN ────────────────────────────────────── --}}
        @else
            <a class="nav-link" href="/">Iniciar sesión</a>
        @endif

    </div>
</nav>