@extends('layouts.app')
@section('content')

<div class="container mt-4">

    {{-- Encabezado --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                @if($esAdmin)
                    <i class="bi bi-clock-history me-2"></i> Gestión de Horarios
                @else
                    <i class="bi bi-clock me-2"></i> Mi Horario de Atención
                @endif
            </h4>
            <p class="text-muted small mb-0">
                @if($esAdmin)
                    Administra los horarios de atención de los médicos.
                @else
                    Consulta tu horario de atención asignado.
                @endif
            </p>
        </div>
    </div>

    {{-- Vista MÉDICO: card visual --}}
    @if(!$esAdmin)
        @php $medico = $medicos->first(); @endphp
        @if($medico && $medico->horario)
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <div class="icon-dash mx-auto mb-3 bg-primary-soft">
                            <i class="bi bi-hourglass-split fs-3 text-primary"></i>
                        </div>
                        <p class="text-muted small mb-1">Hora inicio</p>
                        <h5 class="fw-bold">{{ $medico->horario->hora_inicio }}</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <div class="icon-dash mx-auto mb-3 bg-danger-soft">
                            <i class="bi bi-hourglass-bottom fs-3 text-danger"></i>
                        </div>
                        <p class="text-muted small mb-1">Hora fin</p>
                        <h5 class="fw-bold">{{ $medico->horario->hora_fin }}</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <div class="icon-dash mx-auto mb-3 bg-warning-soft">
                            <i class="bi bi-cup-hot fs-3 text-warning"></i>
                        </div>
                        <p class="text-muted small mb-1">Almuerzo</p>
                        <h5 class="fw-bold">
                            @if($medico->horario->almuerzo_inicio)
                                {{ $medico->horario->almuerzo_inicio }} — {{ $medico->horario->almuerzo_fin }}
                            @else
                                <span class="text-muted fs-6">No asignado</span>
                            @endif
                        </h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3">
                        <div class="icon-dash mx-auto mb-3 bg-success-soft">
                            <i class="bi bi-stopwatch fs-3 text-success"></i>
                        </div>
                        <p class="text-muted small mb-1">Duración por cita</p>
                        <h5 class="fw-bold">{{ $medico->horario->hora_atencion }} min</h5>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span>Aún no tienes un horario asignado. Contacta al administrador.</span>
            </div>
        @endif

    {{-- Vista ADMIN: tabla --}}
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-uppercase small" style="background-color: #0d3b6e; color: white;">
                            <tr>
                                <th class="px-4 py-3">Médico</th>
                                <th>Especialidad</th>
                                <th>Horario</th>
                                <th>Duración por cita</th>
                                <th>Almuerzo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicos as $medico)
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="icon-circle-sm bg-primary-soft flex-shrink-0">
                                                <i class="bi bi-person-badge text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $medico->name }} {{ $medico->Apellidos }}
                                                </div>
                                                <div class="text-muted small">{{ $medico->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($medico->especialidad)
                                            <span class="badge bg-light text-dark border">
                                                {{ $medico->especialidad->Nombre_especialidad }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Sin especialidad</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($medico->horario)
                                            <span class="badge bg-success">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $medico->horario->hora_inicio }} — {{ $medico->horario->hora_fin }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Sin horario
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($medico->horario)
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-stopwatch me-1"></i>
                                                {{ $medico->horario->hora_atencion }} min
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($medico->horario && $medico->horario->almuerzo_inicio)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-cup-hot me-1"></i>
                                                {{ $medico->horario->almuerzo_inicio }} — {{ $medico->horario->almuerzo_fin }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Sin almuerzo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$medico->horario)
                                            <button class="btn btn-success btn-sm rounded-pill btnCrearHorario"
                                                    data-medico='@json($medico)'>
                                                <i class="bi bi-plus-circle me-1"></i> Definir
                                            </button>
                                        @else
                                            <button class="btn btn-primary btn-sm rounded-pill btnEditarHorario"
                                                    data-horario='@json($medico->horario)'>
                                                <i class="bi bi-pencil me-1"></i> Editar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Modales solo Admin --}}
@if($esAdmin)

    {{-- Modal Editar Horario --}}
    <div class="modal fade" id="exampledit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #0d3b6e;">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil me-2"></i> Editar Horario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarhorario">
                        @csrf
                        <input type="hidden" id="horario_id" name="horario_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-hourglass-split me-1"></i> Hora inicio
                                </label>
                                <input type="time" class="form-control" id="editar_hora_inicio"
                                       name="hora_inicio" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-hourglass-bottom me-1"></i> Hora fin
                                </label>
                                <input type="time" class="form-control" id="editar_hora_fin"
                                       name="hora_fin" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-cup-hot me-1"></i> Almuerzo inicio
                                </label>
                                <input type="time" class="form-control" id="editar_almuerzo_inicio"
                                       name="almuerzo_inicio">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-cup-hot me-1"></i> Almuerzo fin
                                </label>
                                <input type="time" class="form-control" id="editar_almuerzo_fin"
                                       name="almuerzo_fin">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">
                                    <i class="bi bi-stopwatch me-1"></i> Duración por cita
                                </label>
                                <select id="editar_hora_atencion" name="hora_atencion"
                                        class="form-select" required>
                                    <option value="20">20 minutos</option>
                                    <option value="30">30 minutos</option>
                                    <option value="40">40 minutos</option>
                                    <option value="45">45 minutos</option>
                                    <option value="60">60 minutos</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary rounded-pill"
                                    data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary rounded-pill">
                                <i class="bi bi-save me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crear Horario --}}
    @include('horarios_modal')

@endif

<style>
    .icon-dash {
        width: 56px; height: 56px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .icon-circle-sm {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .bg-primary-soft   { background-color: #e8f0fb; }
    .bg-danger-soft    { background-color: #fdecea; }
    .bg-warning-soft   { background-color: #fff8e1; }
    .bg-success-soft   { background-color: #e6f9f0; }
</style>

@endsection

@section('javascript')
@parent
@if($esAdmin)
<script>
$(document).ready(function () {

    let modalHorario = new bootstrap.Modal(document.getElementById('modalHorario'));
    let modalEditar  = new bootstrap.Modal(document.getElementById('exampledit'));

    $(document).on('click', '.btnCrearHorario', function () {
        let medico = $(this).data('medico');
        $('#medico_id').val(medico.id);
        $('#tituloModal').text('Definir horario de ' + medico.name + ' ' + medico.Apellidos);
        $('#formHorario')[0].reset();
        modalHorario.show();
    });

    $(document).on('click', '.btnEditarHorario', function () {
        let horario = $(this).data('horario');
        $('#horario_id').val(horario.id);
        $('#editar_hora_inicio').val(horario.hora_inicio);
        $('#editar_hora_fin').val(horario.hora_fin);
        $('#editar_almuerzo_inicio').val(horario.almuerzo_inicio);
        $('#editar_almuerzo_fin').val(horario.almuerzo_fin);
        $('#editar_hora_atencion').val(horario.hora_atencion);
        modalEditar.show();
    });

    $('#formHorario').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/horario',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                mostrarToast('Horario creado correctamente', 'success');
                agregarNotificacion('Horario definido para médico', 'success');
                setTimeout(() => { modalHorario.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            }
        });
    });

    $('#formEditarhorario').on('submit', function (e) {
        e.preventDefault();
        let horarioId = $('#horario_id').val();
        $.ajax({
            url: '/horario/' + horarioId,
            method: 'POST',
            data: {
                _token:          $('meta[name="csrf-token"]').attr('content'),
                _method:         'PUT',
                hora_inicio:     $('#editar_hora_inicio').val(),
                hora_fin:        $('#editar_hora_fin').val(),
                almuerzo_inicio: $('#editar_almuerzo_inicio').val(),
                almuerzo_fin:    $('#editar_almuerzo_fin').val(),
                hora_atencion:   $('#editar_hora_atencion').val(),
            },
            success: function () {
                mostrarToast('Horario actualizado correctamente', 'success');
                agregarNotificacion('Horario de médico actualizado', 'info');
                setTimeout(() => { modalEditar.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al actualizar', 'danger');
            }
        });
    });

});
</script>
@endif
@endsection