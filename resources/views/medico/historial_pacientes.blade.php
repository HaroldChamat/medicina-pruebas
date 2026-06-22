@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clock-history me-2"></i> Mis Pacientes
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.75);">
                Consulta el historial médico de tus pacientes y gestiona tus citas pendientes.
            </p>
        </div>
        <a href="/login" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Buscador --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="buscadorPacientes"
                               class="form-control border-start-0"
                               placeholder="Buscar por nombre o apellido...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filtroCitas" class="form-select">
                        <option value="">Todos los pacientes</option>
                        <option value="con-citas">Con citas registradas</option>
                        <option value="con-pendientes">Con citas pendientes</option>
                        <option value="sin-citas">Sin citas</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <span class="small text-muted" id="contadorPacientes">
                        {{ $pacientes->count() }} paciente(s)
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid de cards --}}
    @if($pacientes->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-person-slash fs-1 d-block mb-3"></i>
                <p class="fw-semibold mb-1">No tienes pacientes registrados</p>
                <small>Aún no tienes citas asociadas a ningún paciente.</small>
            </div>
        </div>
    @else
        <div class="row g-4" id="gridPacientes">
            @foreach($pacientes as $paciente)
                <div class="col-md-6 col-lg-4 paciente-card"
                     data-nombre="{{ strtolower($paciente->name . ' ' . $paciente->Apellidos) }}"
                     data-total="{{ $paciente->total_citas }}"
                     data-pendientes="{{ $paciente->citas_pendientes }}">

                    <div class="card border-0 shadow-sm h-100"
                         style="border-radius: 14px; overflow: hidden;">

                        {{-- Header --}}
                        <div class="card-header border-0 text-white py-3 px-4"
                             style="background: linear-gradient(135deg, #1a7a4a, #2ecc71);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center
                                            justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:48px; height:48px;
                                            background: rgba(255,255,255,0.2);
                                            font-size: 1.2rem;">
                                    {{ strtoupper(substr($paciente->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="fw-bold mb-0 text-truncate">
                                        {{ $paciente->name }} {{ $paciente->Apellidos }}
                                    </p>
                                    <small class="opacity-75">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $paciente->Rut ?? 'Sin RUT' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Cuerpo --}}
                        <div class="card-body px-4 py-3">

                            {{-- Contacto --}}
                            <div class="mb-3">
                                <p class="small text-muted mb-1">
                                    <i class="bi bi-envelope me-1"></i>
                                    {{ $paciente->email }}
                                </p>
                                @if($paciente->telefono)
                                    <p class="small text-muted mb-0">
                                        <i class="bi bi-telephone me-1"></i>
                                        {{ $paciente->telefono }}
                                    </p>
                                @endif
                            </div>

                            <hr class="my-2">

                            {{-- Estadísticas: 4 columnas (solo respecto a este médico) --}}
                            <div class="row g-2 text-center mb-3">

                                {{-- Total --}}
                                <div class="col-3">
                                    <div class="rounded-3 py-2" style="background: #f0f4ff;">
                                        <div class="fw-bold fs-6" style="color: #1a7a4a;">
                                            {{ $paciente->total_citas }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.65rem; line-height: 1.2;">
                                            Total
                                        </div>
                                    </div>
                                </div>

                                {{-- Programadas --}}
                                <div class="col-3">
                                    <div class="rounded-3 py-2"
                                         style="background: {{ $paciente->citas_programadas > 0 ? '#e8f0fb' : '#f8f9fa' }};">
                                        <div class="fw-bold fs-6"
                                             style="color: {{ $paciente->citas_programadas > 0 ? '#1a6fa8' : '#6c757d' }};">
                                            {{ $paciente->citas_programadas }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.65rem; line-height: 1.2;">
                                            Program.
                                        </div>
                                    </div>
                                </div>

                                {{-- Completadas --}}
                                <div class="col-3">
                                    <div class="rounded-3 py-2"
                                         style="background: {{ $paciente->citas_completadas > 0 ? '#e8f5e9' : '#f8f9fa' }};">
                                        <div class="fw-bold fs-6"
                                             style="color: {{ $paciente->citas_completadas > 0 ? '#1a7a4a' : '#6c757d' }};">
                                            {{ $paciente->citas_completadas }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.65rem; line-height: 1.2;">
                                            Complet.
                                        </div>
                                    </div>
                                </div>

                                {{-- Canceladas --}}
                                <div class="col-3">
                                    <div class="rounded-3 py-2"
                                         style="background: {{ $paciente->citas_canceladas > 0 ? '#fdecea' : '#f8f9fa' }};">
                                        <div class="fw-bold fs-6"
                                             style="color: {{ $paciente->citas_canceladas > 0 ? '#c62828' : '#6c757d' }};">
                                            {{ $paciente->citas_canceladas }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.65rem; line-height: 1.2;">
                                            Cancelad.
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Badge clicable de pendientes --}}
                            @if($paciente->citas_pendientes > 0)
                                <button type="button"
                                        class="badge rounded-pill border-0 btn-ver-pendientes w-100 text-start px-3 py-2"
                                        style="background: #fff8e1; color: #856404;
                                               border: 1px solid #ffc107 !important;
                                               cursor: pointer; font-size: 0.78rem;"
                                        data-paciente-id="{{ $paciente->id }}"
                                        data-paciente-nombre="{{ $paciente->name }} {{ $paciente->Apellidos }}">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    {{ $paciente->citas_pendientes }} cita(s) pendiente(s) por atender
                                    <i class="bi bi-chevron-right float-end mt-1"></i>
                                </button>
                            @else
                                <span class="badge rounded-pill px-3 py-2 w-100 text-center"
                                      style="background: #e8f5e9; color: #1a7a4a;
                                             border: 1px solid #4caf50;
                                             font-size: 0.78rem;">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Sin citas pendientes
                                </span>
                            @endif

                        </div>

                        {{-- Footer --}}
                        <div class="card-footer bg-white border-0 px-4 pb-3 pt-0">
                            @if($paciente->citas_completadas > 0)
                                <a href="{{ route('historial.index', $paciente->id) }}"
                                   class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Ver historial médico
                                </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm w-100 rounded-pill"
                                        disabled>
                                    <i class="bi bi-slash-circle me-1"></i>
                                    Sin historial disponible
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center text-muted py-5 d-none" id="sinResultados">
            <i class="bi bi-search fs-2 d-block mb-2"></i>
            No se encontraron pacientes con los filtros aplicados.
        </div>
    @endif

</div>

{{-- Modal citas pendientes --}}
<div class="modal fade" id="modalCitasPendientes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden;">

            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg, #e6a817, #f0c040);">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0">
                        <i class="bi bi-hourglass-split me-2"></i>
                        Citas pendientes por atender
                    </h5>
                    <small class="text-dark opacity-75" id="subtituloPendientes"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                {{-- Spinner de carga --}}
                <div id="spinnerPendientes" class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="text-muted mt-2 small">Cargando citas...</p>
                </div>

                {{-- Contenido --}}
                <div id="contenidoPendientes" class="d-none">

                    {{-- Sin resultados --}}
                    <div id="sinPendientes" class="text-center py-5 text-muted d-none">
                        <i class="bi bi-calendar-check fs-2 d-block mb-2"></i>
                        No hay citas pendientes.
                    </div>

                    {{-- Lista de citas --}}
                    <div id="listaCitasPendientes" class="p-3 d-flex flex-column gap-3"></div>

                </div>
            </div>

            <div class="modal-footer border-0">
                <button class="btn btn-secondary rounded-pill"
                        data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

{{-- Modal Editar Cita (desde citas pendientes) --}}
<div class="modal fade" id="modalEditarCitaPendiente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1a7a4a;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Editar cita
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarCitaPendiente">
                    @csrf
                    <input type="hidden" id="ecp_cita_id">
                    <input type="hidden" id="ecp_medico_id">
                    <input type="hidden" id="ecp_prestacion_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">
                            <i class="bi bi-person-badge me-1"></i> Médico
                        </label>
                        <input id="ecp_medico_nombre" class="form-control" disabled
                               style="background:#f8f9fa; font-weight:600;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">
                            <i class="bi bi-person me-1"></i> Paciente
                        </label>
                        <input id="ecp_paciente_nombre" class="form-control" disabled
                               style="background:#f8f9fa;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">
                            <i class="bi bi-clipboard2-pulse me-1"></i> Prestación
                        </label>
                        <input id="ecp_prestacion_nombre" class="form-control" disabled
                               style="background:#f8f9fa;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-calendar me-1"></i> Fecha
                        </label>
                        <input id="ecp_fecha" type="date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-clock me-1"></i> Hora de atención
                        </label>
                        <select id="ecp_hora" class="form-select" disabled>
                            <option value="">Seleccione una fecha primero</option>
                        </select>
                        <div id="ecp_infoHoras" class="form-text text-muted d-none">
                            <i class="bi bi-info-circle me-1"></i>
                            <span id="ecp_textoInfoHoras"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-flag me-1"></i> Estado
                        </label>
                        <select id="ecp_estado" class="form-select">
                            <option value="Pendiente">⏳ Pendiente</option>
                            <option value="Programada">📅 Programada</option>
                            <option value="Finalizada">✅ Finalizada</option>
                            <option value="Cancelada">❌ Cancelada</option>
                        </select>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    let pacienteIdActual = null;
    let pacienteNombreActual = null;

    // ── FILTROS ───────────────────────────────────────────────────────────
    function filtrar() {
        const texto      = $('#buscadorPacientes').val().toLowerCase().trim();
        const filtro     = $('#filtroCitas').val();
        let visibles     = 0;

        $('.paciente-card').each(function () {
            const nombre     = $(this).data('nombre');
            const total      = parseInt($(this).data('total'));
            const pendientes = parseInt($(this).data('pendientes'));

            const coincideTexto = !texto || nombre.includes(texto);

            let coincideFiltro = true;
            if (filtro === 'con-citas')      coincideFiltro = total > 0;
            if (filtro === 'con-pendientes') coincideFiltro = pendientes > 0;
            if (filtro === 'sin-citas')      coincideFiltro = total === 0;

            if (coincideTexto && coincideFiltro) {
                $(this).show();
                visibles++;
            } else {
                $(this).hide();
            }
        });

        $('#contadorPacientes').text(visibles + ' paciente(s)');
        $('#sinResultados').toggleClass('d-none', visibles > 0);
    }

    $('#buscadorPacientes').on('keyup', filtrar);
    $('#filtroCitas').on('change', filtrar);

    // ── MODAL CITAS PENDIENTES ────────────────────────────────────────────
    const modalPendientes = new bootstrap.Modal(
        document.getElementById('modalCitasPendientes')
    );
    const modalEditarPendiente = new bootstrap.Modal(
        document.getElementById('modalEditarCitaPendiente')
    );

    function cargarCitasPendientes(pacienteId, pacienteNombre) {
        $('#subtituloPendientes').text('Paciente: ' + pacienteNombre);
        $('#spinnerPendientes').removeClass('d-none');
        $('#contenidoPendientes').addClass('d-none');
        $('#listaCitasPendientes').empty();
        $('#sinPendientes').addClass('d-none').html(
            '<i class="bi bi-calendar-check fs-2 d-block mb-2"></i> No hay citas pendientes.'
        );

        $.ajax({
            // Endpoint exclusivo del médico: solo sus propias citas pendientes con este paciente
            url: '/medico/citas-pendientes/' + pacienteId,
            method: 'GET',
            success: function (citas) {
                $('#spinnerPendientes').addClass('d-none');
                $('#contenidoPendientes').removeClass('d-none');

                if (citas.length === 0) {
                    $('#sinPendientes')
                        .removeClass('d-none')
                        .html('<i class="bi bi-calendar-check fs-2 d-block mb-2"></i> Ya no hay citas médicas pendientes para este paciente.');
                    actualizarContadorCard(pacienteId, 0);
                    return;
                }

                actualizarContadorCard(pacienteId, citas.length);

                citas.forEach(function (cita) {
                    const html = `
                        <div class="card border-0 shadow-sm cita-pendiente-item"
                             data-cita-id="${cita.id}"
                             style="border-radius:12px; border-left: 4px solid #ffc107 !important; border-left-style: solid !important;">
                            <div class="card-body py-3 px-4">
                                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                    <div>
                                        <p class="fw-bold mb-1" style="color:#1a7a4a;">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            ${cita.fecha}
                                        </p>
                                        <p class="small text-muted mb-1">
                                            <i class="bi bi-person-badge me-1"></i>
                                            Dr. ${cita.medico}
                                        </p>
                                        <p class="small text-muted mb-0">
                                            <i class="bi bi-clipboard2-pulse me-1"></i>
                                            ${cita.prestacion ?? 'Sin prestación asignada'}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge rounded-pill"
                                              style="background:#fff8e1; color:#856404; border:1px solid #ffc107;">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            ${cita.estado}
                                        </span>
                                        <br>
                                        <small class="text-muted mt-1 d-block">
                                            <i class="bi bi-qr-code me-1"></i>
                                            ${cita.codigo}
                                        </small>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success rounded-pill mt-2 btnEditarCitaPendiente"
                                                data-id="${cita.id}">
                                            <i class="bi bi-pencil-square me-1"></i> Editar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $('#listaCitasPendientes').append(html);
                });
            },
            error: function (xhr) {
                $('#spinnerPendientes').addClass('d-none');
                $('#contenidoPendientes').removeClass('d-none');
                $('#sinPendientes').removeClass('d-none');
                $('#sinPendientes').html(
                    '<i class="bi bi-exclamation-triangle fs-2 d-block mb-2 text-danger"></i>' +
                    (xhr.status === 403
                        ? 'No tienes acceso a las citas de este paciente.'
                        : 'Error al cargar las citas.')
                );
            }
        });
    }

    // Actualiza el badge de "citas pendientes" en la card del paciente,
    // sin recargar la página.
    function actualizarContadorCard(pacienteId, nuevoTotal) {
        const $card = $(`.btn-ver-pendientes[data-paciente-id="${pacienteId}"]`).closest('.paciente-card');
        $card.attr('data-pendientes', nuevoTotal);

        const $btnPendientes = $card.find('.btn-ver-pendientes');

        if (nuevoTotal > 0) {
            $btnPendientes.html(`
                <i class="bi bi-hourglass-split me-1"></i>
                ${nuevoTotal} cita(s) pendiente(s) por atender
                <i class="bi bi-chevron-right float-end mt-1"></i>
            `);
        } else if ($btnPendientes.length) {
            // Reemplazar el botón por el badge de "Sin citas pendientes"
            const $span = $(`
                <span class="badge rounded-pill px-3 py-2 w-100 text-center"
                      style="background: #e8f5e9; color: #1a7a4a;
                             border: 1px solid #4caf50;
                             font-size: 0.78rem;">
                    <i class="bi bi-check-circle me-1"></i>
                    Sin citas pendientes
                </span>`);
            $btnPendientes.replaceWith($span);
        }
    }

    $(document).on('click', '.btn-ver-pendientes', function () {
        pacienteIdActual     = $(this).data('paciente-id');
        pacienteNombreActual = $(this).data('paciente-nombre');

        modalPendientes.show();
        cargarCitasPendientes(pacienteIdActual, pacienteNombreActual);
    });

    // ── ABRIR MODAL EDITAR CITA (desde la lista de pendientes) ────────────
    $(document).on('click', '.btnEditarCitaPendiente', function () {
        const citaId = $(this).data('id');

        $.ajax({
            url: '/citas/' + citaId + '/edit',
            type: 'GET',
            success: function (cita) {
                $('#ecp_cita_id').val(cita.id);
                $('#ecp_medico_id').val(cita.medico_id);
                $('#ecp_prestacion_id').val(cita.prestacion_id);
                $('#ecp_medico_nombre').val(cita.medico.name + ' ' + cita.medico.Apellidos);
                $('#ecp_paciente_nombre').val(cita.paciente.name + ' ' + cita.paciente.Apellidos);
                $('#ecp_prestacion_nombre').val(cita.prestacion ? cita.prestacion.nombre : 'Sin prestación asignada');
                $('#ecp_estado').val(cita.estado);

                const fechaHora = cita.Fecha_y_hora ? cita.Fecha_y_hora.replace('T', ' ') : '';
                const partes    = fechaHora.split(' ');
                const fechaSola = partes[0] ?? '';
                const horaSola  = partes[1] ? partes[1].substring(0, 5) : '00:00';

                $('#ecp_fecha').val(fechaSola);
                $('#ecp_hora').html('<option value="">Cargando horas...</option>').prop('disabled', true);
                $('#ecp_infoHoras').addClass('d-none');

                // Cerrar el modal de pendientes y abrir el de edición
                modalPendientes.hide();
                modalEditarPendiente.show();

                cargarHorasEcp(cita.medico_id, cita.prestacion_id, fechaSola, horaSola);
            },
            error: function (xhr) {
                mostrarToast(
                    xhr.status === 403
                        ? 'No tienes permiso para editar esta cita'
                        : 'No se pudo cargar la información de la cita',
                    'danger'
                );
            }
        });
    });

    function cargarHorasEcp(medicoId, prestacionId, fecha, horaActual) {
        if (!medicoId || !fecha) {
            $('#ecp_hora').html('<option value="">Sin fecha seleccionada</option>').prop('disabled', true);
            return;
        }

        let url = `/citas/horas-disponibles?medico_id=${medicoId}&fecha=${fecha}`;
        if (prestacionId) {
            url += `&prestacion_id=${prestacionId}`;
        }

        fetch(url)
            .then(r => r.json())
            .then(horas => {
                $('#ecp_hora').html('<option value="">Seleccione una hora</option>');

                const horasConActual = (horas.includes(horaActual) || !horaActual)
                    ? horas
                    : [horaActual, ...horas];

                if (horasConActual.length === 0) {
                    $('#ecp_hora').html('<option value="">No hay horas disponibles</option>');
                    $('#ecp_infoHoras').removeClass('d-none');
                    $('#ecp_textoInfoHoras').text('No hay slots disponibles para esta fecha.');
                    return;
                }

                horasConActual.forEach(h => {
                    const selected = h === horaActual ? 'selected' : '';
                    const esActual = h === horaActual ? ' (actual)' : '';
                    $('#ecp_hora').append(
                        `<option value="${h}" ${selected}>${h}${esActual}</option>`
                    );
                });

                $('#ecp_hora').prop('disabled', false);

                if (horas.length > 0) {
                    $('#ecp_infoHoras').removeClass('d-none');
                    $('#ecp_textoInfoHoras').text(`${horas.length} hora(s) disponible(s) según la prestación.`);
                }
            })
            .catch(() => {
                $('#ecp_hora').html('<option value="">Error al cargar horas</option>');
            });
    }

    $(document).on('change', '#ecp_fecha', function () {
        const medicoId     = $('#ecp_medico_id').val();
        const prestacionId = $('#ecp_prestacion_id').val();
        const fecha        = $(this).val();
        if (!medicoId || !fecha) return;

        $('#ecp_hora').html('<option value="">Cargando...</option>').prop('disabled', true);
        cargarHorasEcp(medicoId, prestacionId, fecha, null);
    });

    // ── GUARDAR EDICIÓN DE LA CITA ─────────────────────────────────────────
    $('#formEditarCitaPendiente').on('submit', function (e) {
        e.preventDefault();

        const citaId      = $('#ecp_cita_id').val();
        const fechaVal     = $('#ecp_fecha').val();
        const horaVal      = $('#ecp_hora').val();
        const fechaHora    = (fechaVal && horaVal) ? `${fechaVal} ${horaVal}` : '';
        const nuevoEstado  = $('#ecp_estado').val();

        if (!fechaHora) {
            mostrarToast('Selecciona una fecha y hora válidas', 'warning');
            return;
        }

        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url: '/citas/' + citaId,
            method: 'POST',
            data: {
                _token:       $('meta[name="csrf-token"]').attr('content'),
                _method:      'PUT',
                Fecha_y_hora: fechaHora,
                estado:       nuevoEstado
            },
            success: function () {
                mostrarToast('Cita actualizada correctamente', 'success');
                modalEditarPendiente.hide();

                // Si ya no está pendiente, se removerá visualmente al refrescar la lista
                setTimeout(() => {
                    if (pacienteIdActual) {
                        modalPendientes.show();
                        cargarCitasPendientes(pacienteIdActual, pacienteNombreActual);
                    }
                }, 400);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al actualizar la cita', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar');
            }
        });
    });

});
</script>
@endsection