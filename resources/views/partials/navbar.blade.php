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

                {{-- Mensajes con badge para Admin --}}
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ route('chat.index') }}">
                        <span class="position-relative">
                            <i class="bi bi-chat-dots me-1"></i> Mensajes
                            <span id="navBadgeChat"
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                                  style="font-size:0.6rem; min-width:18px; height:18px;
                                         line-height:1; padding: 3px 5px;
                                         border:2px solid #0d3b6e;">0</span>
                        </span>
                    </a>
                </li>

                {{-- Tickets con badge para Admin --}}
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ route('tickets.index') }}">
                        <span class="position-relative">
                            <i class="bi bi-ticket-detailed me-1"></i> Tickets
                            <span id="navBadgeTickets"
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                                  style="font-size:0.6rem; min-width:18px; height:18px;
                                         line-height:1; padding: 3px 5px;
                                         border:2px solid #0d3b6e;">0</span>
                        </span>
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
                
                {{-- Tickets con badge para Médico --}}
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ route('tickets.index') }}">
                        <span class="position-relative">
                            <i class="bi bi-ticket-detailed me-1"></i> Tickets
                            <span id="navBadgeTickets"
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                                  style="font-size:0.6rem; min-width:18px; height:18px;
                                         line-height:1; padding: 3px 5px;
                                         border:2px solid #0d3b6e;">0</span>
                        </span>
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
                {{-- Mensajes con badge para Paciente --}}
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3" href="{{ route('chat.index') }}">
                        <span class="position-relative">
                            <i class="bi bi-chat-dots me-1"></i> Mensajes
                            <span id="navBadgeChat"
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                                  style="font-size:0.6rem; min-width:18px; height:18px;
                                         line-height:1; padding: 3px 5px;
                                         border:2px solid #0d3b6e;">0</span>
                        </span>
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
                            id="btnNotificaciones" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            style="width:38px; height:38px; padding:0; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-bell-fill fs-6"></i>
                        <span id="badgeNotif"
                              class="position-absolute badge rounded-pill bg-danger"
                              style="display:none; font-size:0.6rem; min-width:18px; height:18px;
                                     line-height:1; padding: 3px 5px;
                                     top:-4px; right:-4px; border:2px solid #0d3b6e; z-index:10;">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow p-0"
                         style="min-width: 320px; max-height: 420px; overflow: hidden;">
                        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center"
                             style="background: #f8f9fa;">
                            <strong class="small"><i class="bi bi-bell me-1"></i> Notificaciones</strong>
                            <button class="btn btn-link btn-sm p-0 text-muted text-decoration-none"
                                    id="btnMarcarLeidas" style="font-size: 0.75rem;">
                                Marcar leídas
                            </button>
                        </div>
                        <ul class="list-unstyled mb-0" id="listaNotif"
                            style="max-height: 340px; overflow-y: auto;">
                            <li class="px-3 py-3 text-muted small text-center" id="sinNotif">
                                <i class="bi bi-bell-slash me-1"></i> Sin notificaciones
                            </li>
                        </ul>
                    </div>
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