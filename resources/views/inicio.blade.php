@extends('layouts.app')
    @section('content')
        <header class="mb-4">
            <nav class="navbar navbar-expand-md navbar-light bg-transparent px-4 py-3 border-0">
                <div class="container-fluid">
                    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ url('/') }}">
                        <i class="bi bi-hospital me-2"></i> Centro Médico
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarContent">
                        <ul class="navbar-nav mx-auto align-items-center gap-3">
                            <li class="nav-item">
                                <a class="nav-link px-3 text-dark fw-semibold" href="{{ url('/') }}">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 text-dark fw-semibold" href="/Especialidad">Especialistas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 text-dark fw-semibold" href="#contacto">Contacto</a>
                            </li>
                        </ul>

                        <div class="d-flex align-items-center">
                            @if(!session('cargo'))
                                <a href="{{ route('login.view') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    Iniciar Sesión
                                </a>
                            @else
                                <form action="/logout" method="GET" class="m-0">
                                    <button class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold btn-sm">
                                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>
        </header>
         
        <section class="hero-section-left">
            <div class="container-narrow-left">
                <div class="hero-content">
                <h1 class="hero-title">Médicos especializados</h1>
                <p class="hero-text">
                    Compra tu bono y agenda con nosotros, para recibir la mejor atención de nuestros médicos especializados.
                </p>
                <a href="/login" class="hero-button">Agenda con nosotros</a>
                </div>
            </div>
        </section>

    <style>
    .hero-section-left {
        background-color: #ffffff;
        padding: 40px 0; /* Un poco menos de espacio arriba y abajo */
        display: flex;
        justify-content: flex-start; /* Alinea el contenido al inicio (izquierda) */
    }

    .container-narrow-left {
        max-width: 700px; /* Lo mantenemos angosto como pediste */
        width: 100%;
        margin-left: 5%; /* Esto lo empuja sutilmente desde el borde izquierdo */
        padding: 20px;
    }

    .hero-title {
        font-size: 28px; /* Un poco más pequeño para que sea estético en formato angosto */
        color: #1a202c;
        margin-bottom: 10px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .hero-text {
        font-size: 16px;
        color: #4a5568;
        line-height: 1.5;
        margin-bottom: 20px;
        max-width: 400px; /* Evita que el texto se estire a la derecha */
    }

    .hero-button {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-family: sans-serif;
        font-size: 15px;
        transition: background 0.3s;
    }

    .hero-button:hover {
        background-color: #0056b3;
    }
    </style>

        </style>

        <a href="https://wa.me/TUNUMEROAQUI" class="btn-whatsapp-flotante" target="_blank">
            <i class="bi bi-whatsapp"></i>
            <span>WhatsApp</span>
        </a>

        <style>
            .btn-whatsapp-flotante {
                position: fixed;
                bottom: 25px;
                right: 25px;
                background-color: #25d366;
                color: white;
                padding: 10px 20px;
                border-radius: 50px;
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
                font-weight: bold;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                z-index: 1050; /* Por encima de todo */
                transition: all 0.3s ease;
            }

            .btn-whatsapp-flotante:hover {
                background-color: #128c7e;
                transform: scale(1.1);
                color: white;
            }

            .btn-whatsapp-flotante i {
                font-size: 1.5rem;
            }

            /* En celulares pequeños solo mostramos el círculo con el icono */
            @media (max-width: 576px) {
                .btn-whatsapp-flotante span {
                    display: none;
                }
                .btn-whatsapp-flotante {
                    padding: 12px;
                    border-radius: 50%;
                }
            }
        </style>
    @endsection