<nav class="navbar navbar-expand-md navbar-dark shadow-sm px-4 mb-4" style="background-color: #0d3b6e;">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
        <i class="bi bi-hospital-fill fs-5"></i>
        <span>Centro Médico</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav mx-auto gap-1">

            @if(session('admin') === 1)
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/citas">
                        <i class="bi bi-calendar-check me-1"></i> Citas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/Especialidad">
                        <i class="bi bi-clipboard2-pulse me-1"></i> Especialidades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/Horario">
                        <i class="bi bi-clock me-1"></i> Horarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/Informacion">
                        <i class="bi bi-file-earmark-medical me-1"></i> Informes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/C_usuario">
                        <i class="bi bi-person-plus me-1"></i> Crear usuario
                    </a>
                </li>

            @elseif(session('cargo') === 'Medico')
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/citas">
                        <i class="bi bi-calendar-check me-1"></i> Mis citas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/Informacion">
                        <i class="bi bi-file-earmark-medical me-1"></i> Mis informes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/Horario">
                        <i class="bi bi-clock me-1"></i> Mi horario
                    </a>
                </li>

            @elseif(session('cargo') === 'Paciente')
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="/citas">
                        <i class="bi bi-calendar-check me-1"></i> Mis citas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3"
                    href="{{ route('historial.index', session('user_id')) }}">
                        <i class="bi bi-clock-history me-1"></i> Mi historial
                    </a>
                </li>
            @endif

        </ul>

        {{-- Notificaciones + usuario + cerrar sesión --}}
        <div class="d-flex align-items-center gap-2">
            @if(session()->has('cargo') || session('admin') === 1)

                {{-- Campana de notificaciones --}}
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm rounded-pill position-relative"
                            id="btnNotificaciones" data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              id="badgeNotif" style="display:none; font-size: 0.6rem;">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow"
                        style="min-width: 300px; max-height: 350px; overflow-y: auto;" id="listaNotif">
                        <li class="dropdown-header fw-bold text-muted px-3 py-2">
                            <i class="bi bi-bell me-1"></i> Notificaciones
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="px-3 py-3 text-muted small text-center" id="sinNotif">
                            Sin notificaciones nuevas
                        </li>
                    </ul>
                </div>

                {{-- Nombre del usuario --}}
                @if(session()->has('cargo'))
                    <span class="text-white opacity-75 small d-none d-md-inline">
                        <i class="bi bi-person-circle me-1"></i>
                        @if(session('cargo') === 'Medico') Dr.
                        @elseif(session('cargo') === 'Paciente') Paciente
                        @endif
                        {{ session('nombre') ?? '' }}
                    </span>
                @endif

                <a href="/logout" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Salir
                </a>

            @else
                <a href="/" class="btn btn-light btn-sm rounded-pill px-3 fw-bold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
                </a>
            @endif
        </div>
    </div>
</nav>