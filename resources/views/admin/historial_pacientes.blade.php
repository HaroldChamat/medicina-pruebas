@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clock-history me-2"></i> Historial de Pacientes
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.75);">
                Consulta el historial médico de los pacientes de tu centro.
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
                <p class="fw-semibold mb-1">No hay pacientes registrados</p>
                <small>Aún no hay pacientes asignados a este centro médico.</small>
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
                             style="background: linear-gradient(135deg, #0d3b6e, #1a6fa8);">
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

                            {{-- Estadísticas: 4 columnas --}}
                            <div class="row g-2 text-center mb-3">

                                {{-- Total --}}
                                <div class="col-3">
                                    <div class="rounded-3 py-2" style="background: #f0f4ff;">
                                        <div class="fw-bold fs-6" style="color: #0d3b6e;">
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

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

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

    $(document).on('click', '.btn-ver-pendientes', function () {
        const pacienteId     = $(this).data('paciente-id');
        const pacienteNombre = $(this).data('paciente-nombre');

        $('#subtituloPendientes').text('Paciente: ' + pacienteNombre);
        $('#spinnerPendientes').removeClass('d-none');
        $('#contenidoPendientes').addClass('d-none');
        $('#listaCitasPendientes').empty();
        $('#sinPendientes').addClass('d-none');

        modalPendientes.show();

        // Traer citas pendientes vía AJAX
        $.ajax({
            url: '/admin/citas-pendientes/' + pacienteId,
            method: 'GET',
            success: function (citas) {
                $('#spinnerPendientes').addClass('d-none');
                $('#contenidoPendientes').removeClass('d-none');

                if (citas.length === 0) {
                    $('#sinPendientes').removeClass('d-none');
                    return;
                }

                citas.forEach(function (cita) {
                    const html = `
                        <div class="card border-0 shadow-sm" style="border-radius:12px; border-left: 4px solid #ffc107 !important; border-left-style: solid !important;">
                            <div class="card-body py-3 px-4">
                                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                    <div>
                                        <p class="fw-bold mb-1" style="color:#0d3b6e;">
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
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $('#listaCitasPendientes').append(html);
                });
            },
            error: function () {
                $('#spinnerPendientes').addClass('d-none');
                $('#contenidoPendientes').removeClass('d-none');
                $('#sinPendientes').removeClass('d-none');
                $('#sinPendientes').html(
                    '<i class="bi bi-exclamation-triangle fs-2 d-block mb-2 text-danger"></i>' +
                    'Error al cargar las citas.'
                );
            }
        });
    });

});
</script>
@endsection