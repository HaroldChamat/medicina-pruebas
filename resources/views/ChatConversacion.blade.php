@extends('layouts.app')
@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                 style="width:48px; height:48px; background: rgba(255,255,255,0.2); font-size: 1.1rem;">
                {{ strtoupper(substr($otroUsuario->name, 0, 1)) }}
            </div>
            <div>
                <h5 class="fw-bold mb-0">
                    @if(session('cargo') === 'Paciente') Dr. @endif
                    {{ $otroUsuario->name }} {{ $otroUsuario->Apellidos }}
                </h5>
                <small style="color: rgba(255,255,255,0.85);">
                    Cita: {{ $cita->codigo_cita ?? 'CIT-'.$cita->id }}
                    · {{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}
                    @if($cita->estado === 'Finalizada')
                        @php
                            $cierre = \Carbon\Carbon::parse($cita->Fecha_y_hora)->addDays(2);
                            $diasRestantes = (int) \Carbon\Carbon::now()->diffInDays($cierre, false);
                        @endphp
                        <span class="badge bg-warning text-dark ms-2">
                            ⏳ Chat cierra en {{ $diasRestantes }} día(s)
                        </span>
                    @endif
                </small>
            </div>
        </div>
        <a href="{{ route('chat.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Área de mensajes --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-0">
            <div id="areaMensajes"
                 style="height: 460px; overflow-y: auto; padding: 1.5rem; background: #f8f9fa;">

                @forelse($mensajes as $msg)
                    @php $esMio = $msg->emisor_id === session('user_id'); @endphp
                    <div class="d-flex mb-3 {{ $esMio ? 'justify-content-end' : 'justify-content-start' }}">
                        <div style="max-width: 70%;">
                            @if(!$esMio)
                                <small class="text-muted d-block mb-1 ms-1">
                                    {{ $msg->emisor->name }} {{ $msg->emisor->Apellidos }}
                                </small>
                            @endif
                            <div class="px-3 py-2 rounded-3 {{ $esMio
                                ? 'text-white'
                                : 'bg-white border' }}"
                                 style="{{ $esMio ? 'background-color: #0d3b6e;' : '' }}">
                                <p class="mb-0" style="line-height: 1.5;">{{ $msg->contenido }}</p>
                            </div>
                            <small class="text-muted d-block mt-1 {{ $esMio ? 'text-end' : '' }}">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                                @if($esMio)
                                    <i class="bi bi-check{{ $msg->leido ? '2' : '' }} ms-1"></i>
                                @endif
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                        Sé el primero en enviar un mensaje
                    </div>
                @endforelse

            </div>
        </div>
    </div>

    {{-- Input de mensaje --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex gap-2 align-items-end">
                <textarea id="inputMensaje"
                          class="form-control"
                          rows="2"
                          placeholder="Escribe un mensaje..."
                          style="resize: none; border-radius: 12px;"></textarea>
                <button id="btnEnviar"
                        class="btn btn-primary px-4 rounded-pill flex-shrink-0"
                        style="background: #0d3b6e; border-color: #0d3b6e;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
            <small class="text-muted mt-1 d-block">
                <i class="bi bi-shield-check me-1"></i>
                Conversación privada · Solo visible para ti y
                @if(session('cargo') === 'Paciente') tu médico @else el paciente @endif
            </small>
        </div>
    </div>

</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    const citaId    = {{ $cita->id }};
    const userId    = {{ session('user_id') }};
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Scroll al fondo al cargar
    const area = document.getElementById('areaMensajes');
    area.scrollTop = area.scrollHeight;

    // ── ENVIAR MENSAJE ───────────────────────────────────────────────────
    function enviarMensaje() {
        const contenido = $('#inputMensaje').val().trim();
        if (!contenido) return;

        $('#btnEnviar').prop('disabled', true);

        $.ajax({
            url: '/chat/' + citaId,
            method: 'POST',
            data: {
                _token:    csrfToken,
                contenido: contenido
            },
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

    $('#btnEnviar').on('click', enviarMensaje);

    $('#inputMensaje').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviarMensaje();
        }
    });

    // ── AGREGAR MENSAJE AL DOM ───────────────────────────────────────────
    function agregarMensaje(msg, esMio) {
        const alineacion = esMio ? 'justify-content-end' : 'justify-content-start';
        const burbuja    = esMio
            ? `style="background-color: #0d3b6e;" class="px-3 py-2 rounded-3 text-white"`
            : `class="px-3 py-2 rounded-3 bg-white border"`;
        const check      = esMio ? '<i class="bi bi-check ms-1"></i>' : '';
        const nombre     = !esMio ? `<small class="text-muted d-block mb-1 ms-1">${msg.emisor}</small>` : '';

        const html = `
            <div class="d-flex mb-3 ${alineacion}" id="msg-${msg.id}">
                <div style="max-width: 70%;">
                    ${nombre}
                    <div ${burbuja}>
                        <p class="mb-0" style="line-height:1.5;">${msg.contenido}</p>
                    </div>
                    <small class="text-muted d-block mt-1 ${esMio ? 'text-end' : ''}">
                        ${msg.hora} ${check}
                    </small>
                </div>
            </div>`;

        $('#areaMensajes').append(html);
        area.scrollTop = area.scrollHeight;
    }

    // ── PUSHER: RECIBIR MENSAJES EN TIEMPO REAL ──────────────────────────
    if (typeof Pusher !== 'undefined') {
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
        });

        const canal = pusher.subscribe('chat.cita.' + citaId);

        canal.bind('nuevo-mensaje', function (data) {
            if (data.emisor_id !== userId) {
                agregarMensaje(data, false);
                mostrarToast('Nuevo mensaje de ' + data.emisor, 'info');

                // Marcar como leído vía AJAX
                $.post('/chat/' + citaId + '/leer', { _token: csrfToken });
            }
        });
    }

});
</script>
@endsection