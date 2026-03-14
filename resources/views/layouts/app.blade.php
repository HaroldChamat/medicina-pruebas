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

        <!-- Pusher -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
       
       <style>
            /* Badge animado estilo WhatsApp */
            #navBadgeChat, #navBadgeTickets {
                animation: pulse-badge 2s infinite;
            }
            #badgeNotif {
                animation: pulse-badge 2s infinite;
            }
            @keyframes pulse-badge {
                0%   { transform: scale(1); }
                50%  { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
        </style>
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
        // ── FUNCIÓN GLOBAL DE NOTIFICACIONES ────────────────────────────────────────
        function agregarNotificacion(mensaje, tipo = 'info', url = '', titulo = '') {
            const iconos = {
                success: 'bi-check-circle-fill text-success',
                danger:  'bi-x-circle-fill text-danger',
                warning: 'bi-exclamation-triangle-fill text-warning',
                info:    'bi-info-circle-fill text-primary'
            };

            const hora = new Date().toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' });
            const contenido = url
                ? `<a href="${url}" class="text-decoration-none text-dark d-block">
                        <div class="small fw-semibold">${titulo || mensaje}</div>
                        ${titulo ? `<div class="small text-muted">${mensaje}</div>` : ''}
                        <div class="text-muted" style="font-size:0.72rem;">${hora}</div>
                   </a>`
                : `<div class="small fw-semibold">${titulo || mensaje}</div>
                   ${titulo ? `<div class="small text-muted">${mensaje}</div>` : ''}
                   <div class="text-muted" style="font-size:0.72rem;">${hora}</div>`;

            const item = `
                <li class="px-3 py-2 border-bottom notif-item">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi ${iconos[tipo]} mt-1"></i>
                        <div class="flex-grow-1">${contenido}</div>
                    </div>
                </li>`;

            $('#sinNotif').hide();
            $('#listaNotif').prepend(item);

            const count = $('.notif-item').length;
            $('#badgeNotif').text(count).show();
        }

        // ── CARGAR NOTIFICACIONES AL INICIO ─────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {

            let dropdownAbierto = false;

            // Cargar notificaciones desde BD — solo badge, no llenar lista aún
            fetch('/notificaciones')
                .then(r => r.json())
                .then(notifs => {
                    if (!notifs.length) return;

                    const noLeidas = notifs.filter(n => !n.leida).length;
                    if (noLeidas > 0) {
                        $('#badgeNotif').text(noLeidas).show();
                    }

                    // Llenar la lista solo cuando el usuario abre el dropdown
                    document.getElementById('btnNotificaciones')?.addEventListener('show.bs.dropdown', function () {
                        if (dropdownAbierto) return;
                        dropdownAbierto = true;

                        $('#sinNotif').hide();
                        notifs.forEach(n => {
                            agregarNotificacion(n.mensaje, n.tipo, n.url, n.titulo);
                        });
                    }, { once: false });
                })
                .catch(() => {});

            // Marcar como leídas al abrir el dropdown
            document.getElementById('btnNotificaciones')?.addEventListener('click', () => {
                setTimeout(() => {
                    $('#badgeNotif').hide();
                    fetch('/notificaciones/leer', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                }, 300);
            });

            // Marcar todas como leídas
            document.getElementById('btnMarcarLeidas')?.addEventListener('click', () => {
                $('#listaNotif').empty();
                $('#sinNotif').show();
                $('#badgeNotif').hide();
                fetch('/notificaciones/leer', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
            });

            
            // ── PUSHER EN TIEMPO REAL ────────────────────────────────────────────────
            @if(session('user_id'))
            if (typeof Pusher !== 'undefined') {
                const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                    cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
                });

                // ── Canal de notificaciones generales ────────────────────────────────
                const canalNotif = pusher.subscribe('notificaciones.{{ session("user_id") }}');

                canalNotif.bind('nueva-notificacion', function (data) {
                    agregarNotificacion(data.mensaje, data.tipo, data.url, data.titulo);
                    mostrarToast(data.titulo + ': ' + data.mensaje, data.tipo);
                    if (typeof actualizarContadores === 'function') actualizarContadores();
                });

                @if(session('cargo') === 'Medico' || session('cargo') === 'Paciente')
                // ── Canal de mensajes chat (paciente-médico) ─────────────────────────
                // Escucha el canal global del usuario para cualquier cita
                canalNotif.bind('nueva-notificacion', function (data) {
                    if (data.url && data.url.startsWith('/chat/')) {
                        actualizarContadores();
                    }
                });
                @endif

                @if(session('cargo') === 'Medico' || session('admin') === 1)
                // ── Canal de tickets ─────────────────────────────────────────────────
                canalNotif.bind('nueva-notificacion', function (data) {
                    if (data.url && data.url.startsWith('/tickets/')) {
                        actualizarContadores();
                    }
                });
                @endif
            }
            @if(session('cargo') === 'Medico')
                // ── Médico: escucha mensajes nuevos de sus citas activas ──────────────
                // Se suscriben dinámicamente cuando llega una notificación de chat
                canalNotif.bind('nueva-notificacion', function(data) {
                    if (!data.url) return;

                    // Si es un mensaje de chat, extraer cita_id y suscribirse
                    if (data.url.startsWith('/chat/')) {
                        const citaId = data.url.split('/chat/')[1];
                        if (citaId) {
                            const canalChat = pusher.subscribe('chat.cita.' + citaId);
                            canalChat.bind('nuevo-mensaje', function(msg) {
                                if (msg.emisor_id != {{ session('user_id') }}) {
                                    actualizarContadores();
                                    mostrarToast('Nuevo mensaje de ' + msg.emisor, 'info');
                                }
                            });
                        }
                    }

                    // Si es un ticket
                    if (data.url.startsWith('/tickets/')) {
                        const ticketId = data.url.split('/tickets/')[1];
                        if (ticketId) {
                            const canalTicket = pusher.subscribe('ticket.' + ticketId);
                            canalTicket.bind('nuevo-mensaje', function(msg) {
                                if (msg.contenido !== '__tomado__' && msg.emisor_id != {{ session('user_id') }}) {
                                    actualizarContadores();
                                    mostrarToast('Nuevo mensaje en ticket', 'info');
                                }
                            });
                        }
                    }
                });
                @endif

                @if(session('cargo') === 'Paciente')
                // ── Paciente: escucha mensajes de chat ───────────────────────────────
                canalNotif.bind('nueva-notificacion', function(data) {
                    if (!data.url) return;
                    if (data.url.startsWith('/chat/')) {
                        const citaId = data.url.split('/chat/')[1];
                        if (citaId) {
                            const canalChat = pusher.subscribe('chat.cita.' + citaId);
                            canalChat.bind('nuevo-mensaje', function(msg) {
                                if (msg.emisor_id != {{ session('user_id') }}) {
                                    actualizarContadores();
                                    mostrarToast('Nuevo mensaje de ' + msg.emisor, 'info');
                                }
                            });
                        }
                    }
                });
                @endif

                @if(session('admin') === 1)
                // ── Admin: escucha mensajes de tickets asignados ─────────────────────
                canalNotif.bind('nueva-notificacion', function(data) {
                    if (!data.url) return;
                    if (data.url.startsWith('/tickets/')) {
                        const ticketId = data.url.split('/tickets/')[1];
                        if (ticketId) {
                            const canalTicket = pusher.subscribe('ticket.' + ticketId);
                            canalTicket.bind('nuevo-mensaje', function(msg) {
                                if (msg.contenido !== '__tomado__' && msg.emisor_id != {{ session('user_id') }}) {
                                    actualizarContadores();
                                    mostrarToast('Nuevo mensaje en ticket', 'info');
                                }
                            });
                        }
                    }
                });
                @endif
            @endif

            // ── CONTADORES DE MENSAJES Y TICKETS ────────────────────────────────────
            @if(session('user_id'))
            function actualizarContadores() {
                fetch('/contadores')
                    .then(r => r.json())
                    .then(data => {
                        // Badge Chat
                        if (data.mensajes > 0) {
                            $('#navBadgeChat').text(data.mensajes).removeClass('d-none');
                        } else {
                            $('#navBadgeChat').addClass('d-none');
                        }

                        // Badge Tickets
                        if (data.tickets > 0) {
                            $('#navBadgeTickets').text(data.tickets).removeClass('d-none');
                        } else {
                            $('#navBadgeTickets').addClass('d-none');
                        }

                        // Badge campana: suma total
                        const total = (data.mensajes || 0) + (data.tickets || 0);
                        if (total > 0) {
                            $('#badgeNotif').text(total).show();
                        }

                        // Badge Notificaciones
                        const totalNuevos = (data.mensajes || 0) + (data.tickets || 0);
                        if (totalNuevos > 0) {
                            $('#badgeNotif').text(totalNuevos)
                                .css('display', 'flex !important');
                        }
                    })
                    .catch(() => {});
            }

            // Cargar al inicio y cada 30 segundos
            actualizarContadores();
            setInterval(actualizarContadores, 30000);
            @endif
        });
        </script>

    </body>
</html>