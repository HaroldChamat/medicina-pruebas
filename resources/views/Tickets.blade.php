@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-ticket-detailed-fill me-2"></i>
                @if(session('cargo') === 'Medico') Mis tickets @else Panel de tickets @endif
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">
                @if(session('cargo') === 'Medico')
                    Solicita soporte al equipo administrativo
                @else
                    Tickets pendientes de atención
                @endif
            </p>
        </div>
        @if(session('cargo') === 'Medico')
            <button class="btn btn-light btn-sm rounded-pill fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
                <i class="bi bi-plus-circle me-1"></i> Nuevo ticket
            </button>
        @endif
    </div>

    {{-- Filtros rápidos --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <button class="btn btn-sm btn-outline-primary filtro-estado active" data-estado="">
            Todos <span class="badge bg-primary ms-1">{{ $tickets->count() }}</span>
        </button>
        <button class="btn btn-sm btn-outline-warning filtro-estado" data-estado="abierto">
            Abiertos <span class="badge bg-warning text-dark ms-1">{{ $tickets->where('estado','abierto')->count() }}</span>
        </button>
        <button class="btn btn-sm btn-outline-info filtro-estado" data-estado="en_progreso">
            En progreso <span class="badge bg-info ms-1">{{ $tickets->where('estado','en_progreso')->count() }}</span>
        </button>
        <button class="btn btn-sm btn-outline-secondary filtro-estado" data-estado="cerrado">
            Cerrados <span class="badge bg-secondary ms-1">{{ $tickets->where('estado','cerrado')->count() }}</span>
        </button>
    </div>

    @if($tickets->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-ticket-detailed fs-1 d-block mb-3"></i>
                <p class="mb-0">No hay tickets aún.</p>
            </div>
        </div>
    @else
        <div class="row g-3" id="listaTickets">
            @foreach($tickets as $ticket)
                <div class="col-12 ticket-item" data-estado="{{ $ticket->estado }}">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">

                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge bg-{{ $ticket->prioridad_color }}">
                                            {{ ucfirst($ticket->prioridad) }}
                                        </span>
                                        <span class="badge bg-{{ $ticket->estado_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->estado)) }}
                                        </span>
                                        <span class="text-muted small">
                                            #{{ $ticket->id }} · {{ $ticket->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>

                                    <h6 class="fw-bold mb-1">{{ $ticket->asunto }}</h6>
                                    <p class="text-muted small mb-1 text-truncate" style="max-width: 500px;">
                                        {{ $ticket->descripcion }}
                                    </p>

                                    <div class="d-flex gap-3 flex-wrap" style="font-size: 0.8rem;">
                                        @if(session('admin') === 1)
                                            <span class="text-muted">
                                                <i class="bi bi-person-badge me-1"></i>
                                                Dr. {{ $ticket->medico->name }} {{ $ticket->medico->Apellidos }}
                                            </span>
                                        @endif
                                        @if($ticket->admin)
                                            <span class="text-muted">
                                                <i class="bi bi-shield-check me-1"></i>
                                                Atendido por: {{ $ticket->admin->name }}
                                            </span>
                                        @else
                                            <span class="text-warning">
                                                <i class="bi bi-hourglass me-1"></i> Sin asignar
                                            </span>
                                        @endif
                                        @if($ticket->cita)
                                            <span class="text-muted">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                {{ $ticket->cita->codigo_cita ?? 'CIT-'.$ticket->cita->id }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-2 align-items-end">
                                    <a href="{{ route('tickets.show', $ticket->id) }}"
                                       class="btn btn-primary btn-sm rounded-pill">
                                        <i class="bi bi-eye me-1"></i> Ver
                                    </a>

                                    @if(session('admin') === 1 && !$ticket->admin_id && $ticket->estado === 'abierto')
                                        <button class="btn btn-success btn-sm rounded-pill btn-tomar"
                                                data-id="{{ $ticket->id }}">
                                            <i class="bi bi-hand-index me-1"></i> Tomar
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>


{{-- ===== MODAL CREAR TICKET ===== --}}
@if(session('cargo') === 'Medico')
<div class="modal fade" id="modalCrearTicket" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #0d3b6e;">
                <h5 class="modal-title text-white">
                    <i class="bi bi-ticket-detailed me-2"></i> Nuevo ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Asunto <span class="text-danger">*</span></label>
                    <input type="text" id="ticket_asunto" class="form-control"
                           placeholder="Resumen breve del problema" maxlength="150">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                    <textarea id="ticket_descripcion" class="form-control" rows="4"
                              placeholder="Describe el problema con el mayor detalle posible..."></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prioridad <span class="text-danger">*</span></label>
                        <select id="ticket_prioridad" class="form-select">
                            <option value="baja">🟢 Baja</option>
                            <option value="media" selected>🟡 Media</option>
                            <option value="alta">🔴 Alta</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cita relacionada</label>
                        <select id="ticket_cita" class="form-select">
                            <option value="">Sin cita relacionada</option>
                            @foreach($citasMedico as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->codigo_cita ?? 'CIT-'.$c->id }} —
                                    {{ $c->paciente->name }} {{ $c->paciente->Apellidos }}
                                    ({{ \Carbon\Carbon::parse($c->Fecha_y_hora)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label fw-semibold">Archivos adjuntos</label>
                    <input type="file" id="ticket_archivos" class="form-control" multiple accept="*/*">
                    <small class="text-muted">Máximo 5MB por archivo</small>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="btnCrearTicket" style="background:#0d3b6e; border-color:#0d3b6e;">
                    <i class="bi bi-send me-1"></i> Enviar ticket
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // ── FILTROS ──────────────────────────────────────────────────────────
    $('.filtro-estado').on('click', function () {
        $('.filtro-estado').removeClass('active');
        $(this).addClass('active');
        const estado = $(this).data('estado');

        $('.ticket-item').each(function () {
            if (!estado || $(this).data('estado') === estado) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // ── CREAR TICKET ─────────────────────────────────────────────────────
    $('#btnCrearTicket').on('click', function () {
        const asunto      = $('#ticket_asunto').val().trim();
        const descripcion = $('#ticket_descripcion').val().trim();
        const prioridad   = $('#ticket_prioridad').val();
        const citaId      = $('#ticket_cita').val();

        if (!asunto || !descripcion) {
            mostrarToast('Completa el asunto y la descripción', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('asunto', asunto);
        formData.append('descripcion', descripcion);
        formData.append('prioridad', prioridad);
        if (citaId) formData.append('cita_id', citaId);

        const archivos = $('#ticket_archivos')[0].files;
        for (let i = 0; i < archivos.length; i++) {
            formData.append('archivos[]', archivos[i]);
        }

        $('#btnCrearTicket').prop('disabled', true).html('<i class="bi bi-hourglass me-1"></i> Enviando...');

        $.ajax({
            url: '/tickets',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                mostrarToast('Ticket creado correctamente', 'success');
                setTimeout(() => window.location.href = '/tickets/' + res.ticket_id, 1000);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al crear ticket', 'danger');
                $('#btnCrearTicket').prop('disabled', false).html('<i class="bi bi-send me-1"></i> Enviar ticket');
            }
        });
    });

    // ── TOMAR TICKET (Admin) ─────────────────────────────────────────────
    $('.btn-tomar').on('click', function () {
        const ticketId = $(this).data('id');
        const btn = $(this);

        btn.prop('disabled', true).html('<i class="bi bi-hourglass"></i>');

        $.ajax({
            url: '/tickets/' + ticketId + '/tomar',
            method: 'POST',
            data: { _token: csrfToken },
            success: function (res) {
                mostrarToast('Ticket tomado correctamente', 'success');
                setTimeout(() => window.location.href = '/tickets/' + ticketId, 1000);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.error ?? 'Error al tomar el ticket', 'danger');
                btn.prop('disabled', false).html('<i class="bi bi-hand-index me-1"></i> Tomar');
            }
        });
    });

    // ── PUSHER: desactivar botón "Tomar" en tiempo real ──────────────────
    @if(session('admin') === 1)
    if (typeof Pusher !== 'undefined') {
        const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
        });

        // Escuchar todos los tickets abiertos
        @foreach($tickets->where('estado', 'abierto') as $t)
        pusher.subscribe('ticket.{{ $t->id }}').bind('nuevo-mensaje', function (data) {
            if (data.contenido === '__tomado__') {
                const btn = $('.btn-tomar[data-id="{{ $t->id }}"]');
                btn.prop('disabled', true)
                   .removeClass('btn-success')
                   .addClass('btn-secondary')
                   .html('<i class="bi bi-lock me-1"></i> Tomado');
            }
        });
        @endforeach
    }
    @endif

});
</script>
@endsection