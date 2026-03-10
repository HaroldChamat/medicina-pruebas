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
                        <i class="bi bi-hash me-1"></i> ID Cita
                    </label>
                    <input type="number" id="filtroCita" class="form-control" placeholder="Ej: 12">
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
                        <th>ID Cita</th>
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
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $cita->id }}</td>
                        <td>{{ $cita->medico->name }}</td>
                        <td>{{ $cita->paciente->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</td>
                        <td>{{ Str::limit($cita->enfermedad->descripcion, 40) }}</td>
                        <td>{{ Str::limit($cita->tratamiento->descripcion, 40) }}</td>
                        <td class="text-center">
                            <button class="btn btn-outline-primary btn-sm">👁 Ver / Editar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Ver / Editar Informe --}}
<div class="modal fade" id="modalInforme" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">📋 Detalle del Informe</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Info de la cita --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">👨‍⚕️ Médico</p>
                        <p class="fw-semibold" id="infoMedico"></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">🧑 Paciente</p>
                        <p class="fw-semibold" id="infoPaciente"></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">📅 Fecha</p>
                        <p class="fw-semibold" id="infoFecha"></p>
                    </div>
                </div>

                <hr>

                <form id="formEditarInforme">
                    @csrf
                    <input type="hidden" id="informe_cita_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">🦠 Diagnóstico</label>
                        <textarea id="campoEnfermedad" name="enfermedad"
                                  class="form-control" rows="3"
                                  {{ session('cargo') === 'Medico' ? '' : '' }}
                                  required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">💊 Tratamiento</label>
                        <textarea id="campoTratamiento" name="tratamiento"
                                  class="form-control" rows="3"
                                  required></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            💾 Guardar cambios
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
    
    // ─── ABRIR MODAL VER/EDITAR ──────────────────────────────────────
    let modalInforme;

    $(document).on('click', '.btnVerInforme', function () {
        let btn = $(this);

        $('#informe_cita_id').val(btn.data('id'));
        $('#infoMedico').text(btn.data('medico'));
        $('#infoPaciente').text(btn.data('paciente'));
        $('#infoFecha').text(btn.data('fecha'));
        $('#campoEnfermedad').val(btn.data('enfermedad'));
        $('#campoTratamiento').val(btn.data('tratamiento'));

        modalInforme = new bootstrap.Modal(document.getElementById('modalInforme'));
        modalInforme.show();
    });

    // ─── GUARDAR CAMBIOS DEL INFORME ─────────────────────────────────
    $('#formEditarInforme').on('submit', function (e) {
        e.preventDefault();

        let citaId = $('#informe_cita_id').val();

        $.ajax({
            url: '/Informe/' + citaId,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
                enfermedad:  $('#campoEnfermedad').val(),
                tratamiento: $('#campoTratamiento').val(),
            },
            success: function () {
                modalInforme.hide();
                location.reload();
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message ?? 'Error al guardar el informe');
            }
        });
    });

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