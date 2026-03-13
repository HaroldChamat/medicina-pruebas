@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <div class="page-header">
        <h3 class="fw-bold">📋 Informes Médicos</h3>
        <p class="small mt-1">Gestión y seguimiento de informes médicos del sistema.</p>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">

                {{-- Filtro médico: solo visible para Admin --}}
                @if(session('admin') === 1)
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person-badge me-1"></i> Médico
                        </label>
                        <select id="filtroMedico" class="form-select">
                            <option value="">Todos</option>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}">
                                    {{ $medico->name }} {{ $medico->Apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    {{-- Para médico: selector oculto fijo en su propio ID --}}
                    <input type="hidden" id="filtroMedico" value="{{ session('user_id') }}">
                @endif

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-qr-code me-1"></i> Código de cita
                    </label>
                    <input type="text" id="filtroCita" class="form-control"
                        placeholder="Ej: CIT-A3F9B21C">
                </div>

                {{-- Botón limpiar --}}
                @if(session('admin') === 1)
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-outline-secondary w-100" id="btnLimpiarInformes">
                            <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="contenedor-tabla-ovalada shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-azul-personalizada">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Médico</th>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Diagnóstico</th>
                        <th>Tratamiento</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas as $cita)
                        <tr data-medico="{{ $cita->medico->id }}" data-cita="{{ $cita->codigo_cita ?? $cita->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-light text-dark border"
                                    style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    {{ $cita->codigo_cita ?? 'CIT-' . $cita->id }}
                                </span>
                            </td>
                            <td>{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</td>
                            <td>{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}</td>
                            <td>{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</td>
                            <td>{{ Str::limit($cita->enfermedad->descripcion, 40) }}</td>
                            <td>{{ Str::limit($cita->tratamiento->descripcion, 40) }}</td>
                            <td class="text-center">
                                <a href="{{ route('informe.show', $cita->id) }}"
                                class="btn btn-success btn-sm rounded-pill">
                                    <i class="bi bi-eye me-1"></i> Ver
                                </a>
                                <a href="{{ route('informe.edit', $cita->id) }}"
                                class="btn btn-warning btn-sm rounded-pill">
                                    <i class="bi bi-pencil me-1"></i> Editar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    // ─── FILTROS ─────────────────────────────────────────────────────
    function filtrar() {
        let medico = $('#filtroMedico').val();
        let cita   = $('#filtroCita').val();

        $('tbody tr').each(function () {
            let medicoFila = $(this).data('medico').toString();
            let citaFila   = $(this).data('cita').toString();

            let mostrar =
                (!medico || medicoFila === medico) &&
                (!cita   || citaFila.includes(cita));

            $(this).toggle(mostrar);
        });
    }

    $('#filtroMedico').on('change', filtrar);
    $('#filtroCita').on('keyup', filtrar);

    $('#btnLimpiarInformes').on('click', function () {
        $('#filtroMedico').val('');
        $('#filtroCita').val('');
        $('tbody tr').show();
    });

    // Aplicar filtro automático al cargar si es médico
    @if(session('cargo') === 'Medico')
        filtrar();
    @endif

});
</script>

<style>
    .table-container-rounded {
        border: 1px solid #dee2e6; 
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    }

    .table-container-rounded table {
        margin-bottom: 0;
        border: none !important;
    }

    .table-container-rounded tbody tr:last-child td {
        border-bottom: 0;
    }

    #btnLimpiarInformes:hover {
        background-color: #ffc107 !important; 
        border-color: #ffc107 !important;
        color: #000 !important; 
        transition: all 0.3s ease; 
    }

    .table-azul-personalizada th {
        background-color: #007bff !important; 
        color: white !important;            
        border-color: #0069d9 !important;  
    }
    
    .rounded-custom {
        border-radius: 20px !important;
        overflow: hidden;
    }

    .contenedor-tabla-ovalada {
        border: 1px solid #dee2e6; 
        border-radius: 15px !important;
        overflow: hidden; 
        background-color: white;
    }

    .contenedor-tabla-ovalada .table {
        border: none !important;
        margin-bottom: 0;
    }

    .contenedor-tabla-ovalada .table-bordered th,
    .contenedor-tabla-ovalada .table-bordered td {
        border: 1px solid #dee2e6 !important;
    }

    .contenedor-tabla-ovalada .table-bordered thead tr:first-child th {
        border-top: none !important;
    }
    .contenedor-tabla-ovalada .table-bordered tr td:first-child,
    .contenedor-tabla-ovalada .table-bordered tr th:first-child {
        border-left: none !important;
    }
    .contenedor-tabla-ovalada .table-bordered tr td:last-child,
    .contenedor-tabla-ovalada .table-bordered tr th:last-child {
        border-right: none !important;
    }
    .contenedor-tabla-ovalada .table-bordered tbody tr:last-child td {
        border-bottom: none !important;
    }

    .table-azul-personalizada th {
        background-color: #007bff !important;
        color: white !important;
        border-color: #0069d9 !important;
    }
</style>
@endsection