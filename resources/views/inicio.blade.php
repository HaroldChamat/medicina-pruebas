@extends('layouts.app')
@section('content')

{{-- ══ NAVBAR ══ --}}
<nav class="navbar navbar-expand-md navbar-dark px-4 py-3" style="background-color: #0d3b6e;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('inicio') }}">
            <i class="bi bi-hospital-fill fs-4"></i>
            <span>Centro Médico</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navInicio">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navInicio">
            <ul class="navbar-nav mx-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold" href="#especialidades">Especialidades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold" href="#por-que">¿Por qué elegirnos?</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold" href="#contacto">Contacto</a>
                </li>
            </ul>
            <a href="{{ route('welcome') }}" class="btn btn-light fw-bold rounded-pill px-4">
                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
            </a>
        </div>
    </div>
</nav>

{{-- ══ HERO ══ --}}
<section class="hero-section d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container position-relative text-white py-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge mb-3 px-3 py-2 fs-6" style="background-color: rgba(255,255,255,0.2);">
                    <i class="bi bi-shield-check me-1"></i> Atención médica de calidad
                </span>
                <h1 class="display-4 fw-bold mb-3 lh-sm">
                    Tu salud en las mejores manos
                </h1>
                <p class="lead mb-4" style="color: rgba(255,255,255,0.85); max-width: 520px;">
                    Contamos con médicos especialistas, agenda de citas en línea e 
                    informes digitales para brindarte la mejor experiencia en salud.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('welcome') }}" class="btn btn-light btn-lg fw-bold rounded-pill px-4">
                        <i class="bi bi-calendar-check me-2"></i>Agendar cita
                    </a>
                    <a href="#especialidades" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        Ver especialidades
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div class="hero-icon-box">
                    <i class="bi bi-heart-pulse-fill" style="font-size: 9rem; color: rgba(255,255,255,0.15);"></i>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ ESTADÍSTICAS ══ --}}
<section class="py-4 shadow-sm" style="background-color: #0d3b6e;">
    <div class="container">
        <div class="row text-center text-white g-3">
            <div class="col-6 col-md-3">
                <div class="fw-bold fs-3">+{{ $totalMedicos }}</div>
                <div class="small opacity-75">Especialistas</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold fs-3">+{{ $totalPacientes }}</div>
                <div class="small opacity-75">Pacientes atendidos</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold fs-3">{{ $totalEspecialidades }}</div>
                <div class="small opacity-75">Especialidades</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-bold fs-3">100%</div>
                <div class="small opacity-75">Atención digital</div>
            </div>
        </div>
    </div>
</section>

{{-- ══ ESPECIALIDADES ══ --}}
<section id="especialidades" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: #0d3b6e;">Nuestras especialidades</h2>
            <p class="text-muted">Atención integral con profesionales certificados</p>
            <div class="mx-auto mt-2" style="width: 60px; height: 3px; background-color: #0d3b6e; border-radius: 2px;"></div>
        </div>

        @php
            $iconos = [
                'Medicina General'  => 'bi-clipboard2-pulse',
                'Cardiología'       => 'bi-heart-pulse',
                'Pediatría'         => 'bi-emoji-smile',
                'Traumatología'     => 'bi-person-arms-up',
                'Neurología'        => 'bi-activity',
                'Oftalmología'      => 'bi-eye',
                'Neumología'        => 'bi-lungs',
            ];
            $chunks = $especialidades->chunk(3);
        @endphp

        <div id="carouselEspecialidades" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

            {{-- Indicadores --}}
            <div class="carousel-indicators">
                @foreach($chunks as $i => $chunk)
                    <button type="button"
                        data-bs-target="#carouselEspecialidades"
                        data-bs-slide-to="{{ $i }}"
                        class="{{ $i === 0 ? 'active' : '' }}"
                        style="background-color: #0d3b6e;">
                    </button>
                @endforeach
            </div>

            {{-- Slides --}}
            <div class="carousel-inner pb-4">
                @foreach($chunks as $i => $chunk)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <div class="row g-4 justify-content-center px-2">
                            @foreach($chunk as $esp)
                                @php
                                    $icono = $iconos[$esp->Nombre_especialidad] ?? 'bi-clipboard2-pulse';
                                @endphp
                                <div class="col-sm-6 col-md-4">
                                    <div class="card h-100 border-0 shadow-sm text-center p-3 card-hover">
                                        <div class="card-body">
                                            <div class="icon-circle mx-auto mb-3">
                                                <i class="bi {{ $icono }} fs-3" style="color: #0d3b6e;"></i>
                                            </div>
                                            <h5 class="fw-bold mb-2" style="color: #0d3b6e;">
                                                {{ $esp->Nombre_especialidad }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Controles --}}
            <button class="carousel-control-prev" type="button"
                data-bs-target="#carouselEspecialidades" data-bs-slide="prev"
                style="filter: invert(1);">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button"
                data-bs-target="#carouselEspecialidades" data-bs-slide="next"
                style="filter: invert(1);">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>

{{-- ══ POR QUÉ ELEGIRNOS ══ --}}
<section id="por-que" class="py-5" style="background-color: #ffffff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: #0d3b6e;">¿Por qué elegirnos?</h2>
            <p class="text-muted">Comprometidos con tu bienestar</p>
            <div class="mx-auto mt-2" style="width: 60px; height: 3px; background-color: #0d3b6e; border-radius: 2px;"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-circle-sm flex-shrink-0">
                        <i class="bi bi-calendar2-check fs-5" style="color: #0d3b6e;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Agenda en línea</h6>
                        <p class="text-muted small mb-0">Reserva tus citas desde cualquier dispositivo, en cualquier momento.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-circle-sm flex-shrink-0">
                        <i class="bi bi-file-earmark-medical fs-5" style="color: #0d3b6e;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Informes digitales</h6>
                        <p class="text-muted small mb-0">Accede a tus informes médicos en PDF desde la plataforma.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-circle-sm flex-shrink-0">
                        <i class="bi bi-people fs-5" style="color: #0d3b6e;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Médicos certificados</h6>
                        <p class="text-muted small mb-0">Todos nuestros especialistas cuentan con certificación vigente.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-circle-sm flex-shrink-0">
                        <i class="bi bi-lock fs-5" style="color: #0d3b6e;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Datos seguros</h6>
                        <p class="text-muted small mb-0">Tu historial médico y datos personales están completamente protegidos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ CTA ══ --}}
<section class="py-5 text-white text-center" style="background-color: #0d3b6e;">
    <div class="container">
        <i class="bi bi-calendar-heart-fill fs-1 mb-3 d-block opacity-75"></i>
        <h2 class="fw-bold mb-3">¿Listo para agendar tu cita?</h2>
        <p class="mb-4 opacity-75">Inicia sesión y agenda con uno de nuestros especialistas hoy mismo.</p>
        <a href="{{ route('welcome') }}" class="btn btn-light btn-lg fw-bold rounded-pill px-5">
            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al sistema
        </a>
    </div>
</section>

{{-- ══ CONTACTO ══ --}}
<section id="contacto" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #0d3b6e;">Contacto</h2>
            <div class="mx-auto mt-2" style="width: 60px; height: 3px; background-color: #0d3b6e; border-radius: 2px;"></div>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 text-center">
                <i class="bi bi-geo-alt-fill fs-2 mb-2" style="color: #0d3b6e;"></i>
                <h6 class="fw-bold">Dirección</h6>
                <p class="text-muted small">Av. Principal 1234, Santiago, Chile</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-telephone-fill fs-2 mb-2" style="color: #0d3b6e;"></i>
                <h6 class="fw-bold">Teléfono</h6>
                <p class="text-muted small">+56 2 1234 5678</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-clock-fill fs-2 mb-2" style="color: #0d3b6e;"></i>
                <h6 class="fw-bold">Horario</h6>
                <p class="text-muted small">Lunes a Viernes: 8:00 - 18:00</p>
            </div>
        </div>
    </div>
</section>

{{-- ══ FOOTER ══ --}}
<footer class="py-3 text-center text-white small" style="background-color: #0a2d57;">
    <div class="container">
        <i class="bi bi-hospital-fill me-1"></i>
        Centro Médico &copy; {{ date('Y') }} — Todos los derechos reservados.
    </div>
</footer>

{{-- ══ WHATSAPP FLOTANTE ══ --}}
<a href="https://wa.me/TUNUMEROAQUI" class="btn-whatsapp-flotante" target="_blank">
    <i class="bi bi-whatsapp"></i>
    <span>WhatsApp</span>
</a>

<style>
    /* Hero */
    .hero-section {
        min-height: 88vh;
        background: linear-gradient(135deg, #0d3b6e 0%, #1a6fa8 60%, #2196b0 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    /* Cards especialidades */
    .card-hover {
        transition: transform 0.25s, box-shadow 0.25s;
        border-radius: 12px;
    }
    .card-hover:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(13,59,110,0.15) !important;
    }

    /* Íconos redondos */
    .icon-circle {
        width: 64px; height: 64px;
        background-color: #e8f0fb;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .icon-circle-sm {
        width: 48px; height: 48px;
        background-color: #e8f0fb;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }

    /* WhatsApp flotante */
    .btn-whatsapp-flotante {
        position: fixed;
        bottom: 25px; right: 25px;
        background-color: #25d366;
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        display: flex; align-items: center; gap: 10px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 1050;
        transition: all 0.3s ease;
    }
    .btn-whatsapp-flotante:hover {
        background-color: #128c7e;
        transform: scale(1.08);
        color: white;
    }
    .btn-whatsapp-flotante i { font-size: 1.4rem; }
    @media (max-width: 576px) {
        .btn-whatsapp-flotante span { display: none; }
        .btn-whatsapp-flotante { padding: 12px; border-radius: 50%; }
        .hero-section { min-height: 70vh; }
    }
</style>

@endsection
