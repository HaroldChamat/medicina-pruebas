@extends('layouts.app')
@section('content')

<div class="container mt-4">

    {{-- Encabezado --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clock-history me-2"></i>
                Historial Médico
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">
                Paciente: <strong>{{ $paciente->name }} {{ $paciente->Apellidos }}</strong>
                <span class="badge bg-secondary ms-2">{{ $paciente->Rut }}</span>
            </p>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Buscador --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-search me-1"></i> Buscar por médico o diagnóstico
                    </label>
                    <input type="text" id="buscadorHistorial" class="form-control"
                           placeholder="Ej: Cardiología, Dr. López...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-calendar me-1"></i> Filtrar por año
                    </label>
                    <select id="filtroAnio" class="form-select">
                        <option value="">Todos los años</option>
                        @foreach($historial->groupBy(fn($c) => \Carbon\Carbon::parse($c->Fecha_y_hora)->year)->keys()->sortDesc() as $anio)
                            <option value="{{ $anio }}">{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" id="btnLimpiarHistorial">
                        <i class="bi bi-x-circle me-1"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sin resultados --}}
    @if($historial->isEmpty())
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <span>Este paciente aún no tiene citas finalizadas registradas.</span>
        </div>
    @else
        {{-- Contador --}}
        <p class="small mb-3 fw-semibold" style="color: rgba(255,255,255,0.85);" id="contadorResultados">
            Mostrando <strong>{{ $historial->count() }}</strong> registro(s)
        </p>

        {{-- Cards del historial --}}
        <div class="row g-4" id="listaHistorial">
            @foreach($historial as $cita)
                <div class="col-md-6 historial-item"
                     data-medico="{{ strtolower($cita->medico->name . ' ' . $cita->medico->Apellidos) }}"
                     data-diagnostico="{{ strtolower(optional($cita->enfermedad)->descripcion ?? '') }}"
                     data-anio="{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->year }}">

                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center text-white"
                             style="background-color: #0d3b6e;">
                            <span class="fw-semibold">
                                   <i class="bi bi-file-earmark-medical me-1"></i>
                                    {{ $cita->codigo_cita ?? 'CIT-' . $cita->id }}
                            </span>
                            <small>
                                <i class="bi bi-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="card-body">

                            <p class="mb-2">
                                <span class="text-muted small">
                                    <i class="bi bi-person-badge me-1"></i> Médico
                                </span><br>
                                <strong>{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</strong>
                                @foreach($cita->medico->especialidades as $esp)
                                    <span class="badge bg-light text-dark ms-1 small">
                                        {{ $esp->Nombre_especialidad }}
                                    </span>
                                @endforeach
                            </p>

                            <hr class="my-2">

                            <p class="mb-2">
                                <span class="text-muted small">
                                    <i class="bi bi-virus me-1"></i> Diagnóstico
                                </span><br>
                                {{ optional($cita->enfermedad)->descripcion ?? 'No registrado' }}
                            </p>

                            <p class="mb-0">
                                <span class="text-muted small">
                                    <i class="bi bi-capsule me-1"></i> Tratamiento
                                </span><br>
                                {{ optional($cita->tratamiento)->descripcion ?? 'No registrado' }}
                            </p>

                        </div>
                        <div class="card-footer bg-white border-0 text-end">
                            <a href="{{ route('informe.pdf', $cita->id) }}"
                               class="btn btn-outline-danger btn-sm rounded-pill"
                               target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-muted small mt-3 d-none" id="sinResultados">
            <i class="bi bi-search me-1"></i> No se encontraron resultados para tu búsqueda.
        </p>
    @endif

</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    function filtrarHistorial() {
        const texto = $('#buscadorHistorial').val().toLowerCase().trim();
        const anio  = $('#filtroAnio').val();
        let visibles = 0;

        $('.historial-item').each(function () {
            const medico      = $(this).data('medico');
            const diagnostico = $(this).data('diagnostico');
            const anioItem    = $(this).data('anio').toString();

            const coincideTexto = !texto ||
                medico.includes(texto) ||
                diagnostico.includes(texto);

            const coincideAnio = !anio || anioItem === anio;

            if (coincideTexto && coincideAnio) {
                $(this).show();
                visibles++;
            } else {
                $(this).hide();
            }
        });

        $('#contadorResultados').html(
            'Mostrando <strong>' + visibles + '</strong> registro(s)'
        );
        $('#sinResultados').toggleClass('d-none', visibles > 0);
    }

    $('#buscadorHistorial').on('keyup', filtrarHistorial);
    $('#filtroAnio').on('change', filtrarHistorial);

    $('#btnLimpiarHistorial').on('click', function () {
        $('#buscadorHistorial').val('');
        $('#filtroAnio').val('');
        filtrarHistorial();
    });

});
</script>
@endsection