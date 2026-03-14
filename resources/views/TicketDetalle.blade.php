@extends('layouts.app')
@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-ticket-detailed me-2"></i>
                Ticket #{{ $ticket->id }}
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-{{ $ticket->prioridad_color }}">
                    {{ ucfirst($ticket->prioridad) }}
                </span>
                <span class="badge bg-{{ $ticket->estado_color }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->estado)) }}
                </span>
                <span style="color:rgba(255,255,255,0.8); font-size:0.8rem;">
                    {{ $ticket->created_at->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(session('admin') === 1 && $ticket->admin_id === session('user_id') && $ticket->estado !== 'cerrado')
                <button class="btn btn-warning btn-sm rounded-pill" id="btnCerrarTicket">
                    <i class="bi bi-lock me-1"></i> Cerrar ticket
                </button>
            @endif
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- Columna izquierda: info + archivos --}}
        <div class="col-md-4">

            {{-- Info del ticket --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header fw-semibold text-white" style="background:#0d3b6e;">
                    <i class="bi bi-info-circle me-1"></i> Información
                </div>
                <div class="card-body">
                    <p class="fw-bold mb-2">{{ $ticket->asunto }}</p>
                    <p class="text-muted small mb-3" style="white-space: pre-wrap;">{{ $ticket->descripcion }}</p>

                    <hr>

                    <div class="small">
                        <p class="mb-1">
                            <i class="bi bi-person-badge me-1 text-primary"></i>
                            <strong>Médico:</strong>
                            Dr. {{ $ticket->medico->name }} {{ $ticket->medico->Apellidos }}
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-shield-check me-1 text-success"></i>
                            <strong>Admin:</strong>
                            {{ $ticket->admin ? $ticket->admin->name . ' ' . $ticket->admin->Apellidos : 'Sin asignar' }}
                        </p>
                        @if($ticket->cita)
                            <p class="mb-1">
                                <i class="bi bi-calendar-check me-1 text-warning"></i>
                                <strong>Cita:</strong>
                                {{ $ticket->cita->codigo_cita ?? 'CIT-'.$ticket->cita->id }}
                                ({{ \Carbon\Carbon::parse($ticket->cita->Fecha_y_hora)->format('d/m/Y') }})
                            </p>
                        @endif
                        @if($ticket->tomado_en)
                            <p class="mb-0">
                                <i class="bi bi-clock me-1 text-info"></i>
                                <strong>Tomado:</strong>
                                {{ \Carbon\Carbon::parse($ticket->tomado_en)->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Archivos adjuntos --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header fw-semibold text-white" style="background:#0d3b6e;">
                    <i class="bi bi-paperclip me-1"></i> Archivos
                    <span class="badge bg-light text-dark ms-1">{{ $ticket->archivos->count() }}</span>
                </div>
                <div class="card-body p-2" id="listaArchivos">
                    @forelse($ticket->archivos as $archivo)
                        <a href="{{ asset('storage/' . $archivo->ruta) }}"
                           target="_blank"
                           class="d-flex align-items-center gap-2 p-2 rounded text-decoration-none text-dark hover-bg mb-1">
                            <i class="bi bi-file-earmark fs-5 text-primary"></i>
                            <div class="min-w-0">
                                <p class="mb-0 small fw-semibold text-truncate">{{ $archivo->nombre_original }}</p>
                                <small class="text-muted">{{ $archivo->emisor->name }}</small>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted small text-center py-2 mb-0" id="sinArchivos">
                            Sin archivos adjuntos
                        </p>
                    @endforelse
                </div>
                @if($ticket->estado !== 'cerrado')
                    <div class="card-footer bg-white border-0 p-2">
                        <div class="d-flex gap-2">
                            <input type="file" id="inputArchivo" class="form-control form-control-sm">
                            <button class="btn btn-sm btn-outline-primary flex-shrink-0" id="btnSubirArchivo">
                                <i class="bi bi-upload"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- Columna derecha: mensajes --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold text-white d-flex justify-content-between"
                     style="background:#0d3b6e;">
                    <span><i class="bi bi-chat-dots me-1"></i> Conversación</span>
                    <span class="badge bg-light text-dark">{{ $ticket->mensajes->count() }} mensajes</span>
                </div>

                {{-- Mensajes --}}
                <div id="areaMensajes"
                     style="height: 420px; overflow-y: auto; padding: 1.2rem; background: #f8f9fa;">

                    @forelse($ticket->mensajes as $msg)
                        @php $esMio = $msg->emisor_id === session('user_id'); @endphp
                        <div class="d-flex mb-3 {{ $esMio ? 'justify-content-end' : 'justify-content-start' }}">
                            <div style="max-width: 75%;">
                                @if(!$esMio)
                                    <small class="text-muted d-block mb-1 ms-1">
                                        {{ $msg->emisor->name }} {{ $msg->emisor->Apellidos }}
                                    </small>
                                @endif
                                <div class="px-3 py-2 rounded-3 {{ $esMio ? 'text-white' : 'bg-white border' }}"
                                     style="{{ $esMio ? 'background-color: #0d3b6e;' : '' }}">
                                    <p class="mb-0" style="line-height:1.5; white-space: pre-wrap;">{{ $msg->contenido }}</p>
                                </div>
                                <small class="text-muted d-block mt-1 {{ $esMio ? 'text-end' : '' }}">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                            Inicia la conversación
                        </div>
                    @endforelse

                </div>

                {{-- Input --}}
                @if($ticket->estado !== 'cerrado')
                    @if(session('cargo') === 'Medico' || (session('admin') === 1 && $ticket->admin_id === session('user_id')))
                        <div class="card-footer bg-white border-top p-3">
                            <div class="d-flex gap-2 align-items-end">
                                <textarea id="inputMensaje" class="form-control" rows="2"
                                          placeholder="Escribe tu mensaje..." style="resize:none; border-radius:12px;"></textarea>
                                <button id="btnEnviar" class="btn btn-primary px-4 rounded-pill flex-shrink-0"
                                        style="background:#0d3b6e; border-color:#0d3b6e;">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>
                    @elseif(session('admin') === 1 && !$ticket->admin_id)
                        <div class="card-footer bg-light text-center text-muted small py-3">
                            <i class="bi bi-lock me-1"></i>
                            Toma este ticket para poder responder
                        </div>
                    @endif
                @else
                    <div class="card-footer bg-light text-center text-muted small py-3">
                        <i class="bi bi-lock-fill me-1"></i> Ticket cerrado
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    const ticketId  = {{ $ticket->id }};
    const userId    = {{ session('user_id') }};
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const area      = document.getElementById('areaMensajes');

    if (area) area.scrollTop = area.scrollHeight;

    // ── ENVIAR MENSAJE ───────────────────────────────────────────────────
    function enviarMensaje() {
        const contenido = $('#inputMensaje').val().trim();
        if (!contenido) return;

        $('#btnEnviar').prop('disabled', true);

        $.ajax({
            url: '/tickets/' + ticketId + '/mensaje',
            method: 'POST',
            data: { _token: csrfToken, contenido },
            success: function (res) {
                $('#inputMensaje').val('');
                agregarMensaje(res.mensaje, true);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.error ?? 'Error al enviar', 'danger');
            },
            complete: function () {
                $('#btnEnviar').prop('disabled', false);
                $('#inputMensaje').focus();
            }
        });
    }

    $('#btnEnviar')?.on('click', enviarMensaje);
    $('#inputMensaje')?.on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviarMensaje(); }
    });

    // ── AGREGAR MENSAJE AL DOM ───────────────────────────────────────────
    function agregarMensaje(msg, esMio) {
        const alineacion = esMio ? 'justify-content-end' : 'justify-content-start';
        const burbuja    = esMio
            ? `style="background-color:#0d3b6e;" class="px-3 py-2 rounded-3 text-white"`
            : `class="px-3 py-2 rounded-3 bg-white border"`;
        const nombre = !esMio
            ? `<small class="text-muted d-block mb-1 ms-1">${msg.emisor}</small>` : '';

        $('#areaMensajes').append(`
            <div class="d-flex mb-3 ${alineacion}">
                <div style="max-width:75%;">
                    ${nombre}
                    <div ${burbuja}>
                        <p class="mb-0" style="line-height:1.5;">${msg.contenido}</p>
                    </div>
                    <small class="text-muted d-block mt-1 ${esMio ? 'text-end' : ''}">
                        ${msg.hora}
                    </small>
                </div>
            </div>`);

        area.scrollTop = area.scrollHeight;
    }

    // ── SUBIR ARCHIVO ────────────────────────────────────────────────────
    $('#btnSubirArchivo')?.on('click', function () {
        const archivo = $('#inputArchivo')[0].files[0];
        if (!archivo) { mostrarToast('Selecciona un archivo', 'warning'); return; }

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('archivo', archivo);

        $(this).prop('disabled', true);

        $.ajax({
            url: '/tickets/' + ticketId + '/archivo',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                mostrarToast('Archivo subido correctamente', 'success');
                $('#inputArchivo').val('');
                $('#sinArchivos').hide();
                setTimeout(() => location.reload(), 1000);
            },
            error: function () {
                mostrarToast('Error al subir el archivo', 'danger');
            },
            complete: function () {
                $('#btnSubirArchivo').prop('disabled', false);
            }
        });
    });

    // ── CERRAR TICKET ────────────────────────────────────────────────────
    $('#btnCerrarTicket')?.on('click', function () {
        if (!confirm('¿Cerrar este ticket?')) return;

        $.ajax({
            url: '/tickets/' + ticketId + '/cerrar',
            method: 'POST',
            data: { _token: csrfToken },
            success: function () {
                mostrarToast('Ticket cerrado', 'warning');
                setTimeout(() => location.reload(), 1000);
            }
        });
    });

    // ── PUSHER ───────────────────────────────────────────────────────────
    if (typeof Pusher !== 'undefined') {
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
        });

        const canal = pusher.subscribe('ticket.' + ticketId);

        canal.bind('nuevo-mensaje', function (data) {
            if (data.contenido !== '__tomado__' && data.emisor_id !== userId) {
                agregarMensaje(data, false);
                mostrarToast('Nuevo mensaje de ' + data.emisor, 'info');
            }
        });
    }

});
</script>
@endsection