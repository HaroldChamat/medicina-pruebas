<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
              rel="stylesheet"
              integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
              crossorigin="anonymous">
        
        <!-- Bootstrap Icons -->  
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


        <!-- Estilos propios -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>

    <body class="position-relative">

        {{-- Corazones decorativos de fondo --}}
       <div class="position-fixed top-0 end-0 p-5 d-none d-lg-block" style="opacity: 0.05; z-index: -1; pointer-events: none;">
            <i class="bi bi-heart-pulse-fill" style="font-size: 18rem; color: white;"></i>
        </div>
        <div class="position-fixed top-0 start-0 p-5 d-none d-lg-block" style="opacity: 0.05; z-index: -1; pointer-events: none;">
            <i class="bi bi-heart-pulse-fill" style="font-size: 18rem; color: white; transform: scaleX(-1);"></i>
        </div>

        {{-- Navbar global por rol --}}
        @if(!Request::is('/') && !(Request::is('login') && !session()->has('cargo')))
        @include('partials.navbar')
        @endif

        <main>
            @yield('content')
        </main>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
                crossorigin="anonymous"></script>

        @yield('javascript')

        {{-- ── TOAST CONTAINER ── --}}
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999" id="toastContainer"></div>

        <script>
        // ── FUNCIÓN GLOBAL DE TOAST ──────────────────────────────────────────────────
        function mostrarToast(mensaje, tipo = 'success') {
            const iconos = {
                success: 'bi-check-circle-fill',
                danger:  'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info:    'bi-info-circle-fill'
            };
            const colores = {
                success: '#1a7a4a',
                danger:  '#dc3545',
                warning: '#ffc107',
                info:    '#0d3b6e'
            };

            const id = 'toast_' + Date.now();
            const html = `
                <div id="${id}" class="toast align-items-center text-white border-0 mb-2 show"
                    style="background-color: ${colores[tipo]}; border-radius: 10px; min-width: 280px;">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2">
                            <i class="bi ${iconos[tipo]} fs-5"></i>
                            <span>${mensaje}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                onclick="document.getElementById('${id}').remove()"></button>
                    </div>
                </div>`;

            $('#toastContainer').append(html);
            setTimeout(() => $('#' + id).remove(), 4000);
        }

        // ── FUNCIÓN GLOBAL DE NOTIFICACIONES ────────────────────────────────────────
        function agregarNotificacion(mensaje, tipo = 'info') {
            const iconos = {
                success: 'bi-check-circle-fill text-success',
                danger:  'bi-x-circle-fill text-danger',
                warning: 'bi-exclamation-triangle-fill text-warning',
                info:    'bi-info-circle-fill text-primary'
            };

            const hora = new Date().toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' });
            const item = `
                <li class="px-3 py-2 border-bottom notif-item">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi ${iconos[tipo]} mt-1"></i>
                        <div>
                            <div class="small fw-semibold">${mensaje}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">${hora}</div>
                        </div>
                    </div>
                </li>`;

            $('#sinNotif').hide();
            $('#listaNotif').append(item);

            // Actualiza badge
            const count = $('.notif-item').length;
            $('#badgeNotif').text(count).show();
        }

        // Limpiar badge al abrir el dropdown
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btnNotificaciones')?.addEventListener('click', () => {
                setTimeout(() => $('#badgeNotif').hide(), 300);
            });
        });
        </script>

    </body>
</html>