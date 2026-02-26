@extends('layouts.app')
@section('content')



    <div class="container mt-4">

        {{-- Título según rol --}}
        @if($esAdmin)
            <h4 class="mb-4">⚙️ Gestión de Horarios</h4>
        @else
            <h4 class="mb-4">🕐 Mi Horario de Atención</h4>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th>Médico</th>
                            <th>Horario</th>
                            <th>Duración por cita</th>
                            <th>Almuerzo</th>
                            {{-- Columna acciones: solo Admin --}}
                            @if($esAdmin)
                                <th>Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicos as $medico)
                            <tr>
                                <td>
                                    <strong>{{ $medico->name }} {{ $medico->Apellidos }}</strong>
                                </td>

                                <td>
                                    @if($medico->horario)
                                        <span class="badge bg-success">
                                            {{ $medico->horario->hora_inicio }} — {{ $medico->horario->hora_fin }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger">Sin horario</span>
                                    @endif
                                </td>

                                <td>
                                    @if($medico->horario)
                                        {{ $medico->horario->hora_atencion }} min
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if($medico->horario && $medico->horario->almuerzo_inicio)
                                        {{ $medico->horario->almuerzo_inicio }} — {{ $medico->horario->almuerzo_fin }}
                                    @else
                                        <span class="text-muted">Sin almuerzo</span>
                                    @endif
                                </td>

                                {{-- Acciones: solo Admin --}}
                                @if($esAdmin)
                                    <td>
                                        @if(!$medico->horario)
                                            <button class="btn btn-success btn-sm btnCrearHorario"
                                                    data-medico='@json($medico)'>
                                                Definir horario
                                            </button>
                                        @else
                                            <button class="btn btn-primary btn-sm btnEditarHorario"
                                                    data-horario='@json($medico->horario)'>
                                                Editar horario
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== MODALES: solo se renderizan para Admin ===== --}}
    @if($esAdmin)

        {{-- Modal Editar Horario --}}
        <div class="modal fade" id="exampledit" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Horario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarhorario">
                            @csrf
                            <input type="hidden" id="horario_id" name="horario_id">

                            <div class="mb-3">
                                <label class="form-label">Hora de inicio</label>
                                <input type="time" class="form-control" id="editar_hora_inicio" name="hora_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hora de fin</label>
                                <input type="time" class="form-control" id="editar_hora_fin" name="hora_fin" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Almuerzo inicio</label>
                                <input type="time" class="form-control" id="editar_almuerzo_inicio" name="almuerzo_inicio">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Almuerzo fin</label>
                                <input type="time" class="form-control" id="editar_almuerzo_fin" name="almuerzo_fin">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Duración por cita (minutos)</label>
                                <select id="editar_hora_atencion" name="hora_atencion" class="form-select" required>
                                    <option value="20">20 minutos</option>
                                    <option value="30">30 minutos</option>
                                    <option value="40">40 minutos</option>
                                    <option value="45">45 minutos</option>
                                    <option value="60">60 minutos</option>
                                </select>
                            </div>

                            <div class="modal-footer px-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Crear Horario --}}
        @include('horarios_modal')

    @endif

@endsection

@section('javascript')
@parent

@if($esAdmin)
<script>
$(document).ready(function () {

    let modalHorario = new bootstrap.Modal(document.getElementById('modalHorario'));
    let modalEditar  = new bootstrap.Modal(document.getElementById('exampledit'));

    // ─── Abrir modal CREAR ───────────────────────────────────────────
    $(document).on('click', '.btnCrearHorario', function () {
        let medico = $(this).data('medico');
        $('#medico_id').val(medico.id);
        $('#tituloModal').text(`Definir horario de ${medico.name} ${medico.Apellidos}`);
        $('#formHorario')[0].reset();
        modalHorario.show();
    });

    // ─── Abrir modal EDITAR ──────────────────────────────────────────
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

    // ─── SUBMIT CREAR ────────────────────────────────────────────────
    $('#formHorario').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '/horario',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                modalHorario.hide();
                location.reload();
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message ?? 'Error al guardar');
            }
        });
    });

    // ─── SUBMIT EDITAR ───────────────────────────────────────────────
    $('#formEditarhorario').on('submit', function (e) {
        e.preventDefault();
        let horarioId = $('#horario_id').val();
        $.ajax({
            url: '/horario/' + horarioId,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
                hora_inicio:     $('#editar_hora_inicio').val(),
                hora_fin:        $('#editar_hora_fin').val(),
                almuerzo_inicio: $('#editar_almuerzo_inicio').val(),
                almuerzo_fin:    $('#editar_almuerzo_fin').val(),
                hora_atencion:   $('#editar_hora_atencion').val(),
            },
            success: function () {
                modalEditar.hide();
                location.reload();
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message ?? 'Error al actualizar');
            }
        });
    });

});
</script>
@endif

@endsection