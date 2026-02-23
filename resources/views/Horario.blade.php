@extends('layouts.app')
    @section('content')
        <header>
            <div class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="/citas">Citas</a>
                </div>

                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="/">Ir a inicio</a>
                </div>

                @if(session('admin') === 1 || session('cargo') === 'Medico' || session('cargo') === 'Paciente')
                    <button type="button" class="btn btn-outline-danger">
                        <a href="/logout" class="nav-link">Cerrar Sesión</a>
                    </button>
                @endif
                
            </div>
        </header>


        <div class="container">
            <h4>Horarios de Médicos</h4>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Médico</th>
                        <th>Horario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicos as $medico)
                        <tr>
                            <td>{{ $medico->name }} {{ $medico->Apellidos }}</td>

                            <td>
                                @if($medico->horario)
                                    {{ $medico->horario->hora_inicio }} - {{ $medico->horario->hora_fin }}
                                    <br>
                                    <small>
                                        Almuerzo:
                                        {{ $medico->horario->almuerzo_inicio ?? '—' }} -
                                        {{ $medico->horario->almuerzo_fin ?? '—' }}
                                    </small>
                                @else
                                    <span class="text-danger">Sin horario</span>
                                @endif
                            </td>

                            <td>
                                @if(!$medico->horario)
                                    <button
                                        class="btn btn-success btnCrearHorario"
                                        data-medico='@json($medico)'>
                                        Definir horario
                                    </button>
                                @else
                                    <button class="btn btn-secondary btnYaTieneHorario">
                                        Definir horario
                                    </button>

                                    <button
                                        class="btn btn-primary btnEditarHorario"
                                        data-horario='@json($medico->horario)'>
                                        Editar horario
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="exampledit" tabindex="-1" aria-labelledby="exampleditLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleditLabel">Editar</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form id="formEditarhorario">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="horario_id" name="horario_id">
                            <div class="mb-3">
                                <label for="editar_hora_inicio" class="form-label">Hora de inicio</label>
                                <input type="time" class="form-control" id="editar_hora_inicio" name="hora_inicio" required>
                            </div>

                            <div class="mb-3">
                                <label for="editar_hora_fin" class="form-label">Hora de fin</label>
                                <input type="time" class="form-control" id="editar_hora_fin" name="hora_fin" required>
                            </div>

                            <div class="mb-3">
                                <label for="editar_almuerzo_inicio" class="form-label">Almuerzo inicio</label>
                                <input type="time" class="form-control" id="editar_almuerzo_inicio" name="almuerzo_inicio">
                            </div>

                            <div class="mb-3">
                                <label for="editar_almuerzo_fin" class="form-label">Almuerzo fin</label>
                                <input type="time" class="form-control" id="editar_almuerzo_fin" name="almuerzo_fin">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hora de atención (minutos)</label>
                                <select id="editar_hora_atencion" name="hora_atencion" class="form-select" required>
                                    <option value="20">20 minutos</option>
                                    <option value="30">30 minutos</option>
                                    <option value="40">40 minutos</option>
                                    <option value="50">50 minutos</option>
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

        @include('horarios_modal')
    @endsection

    @section('javascript')
    @parent

        <script>

            $(document).ready(function () {
                // todo tu JS

                let modalHorario = new bootstrap.Modal(document.getElementById('modalHorario'));
                let modalEditar = new bootstrap.Modal(
                    document.getElementById('exampledit')
                );

                $(document).on('click', '.btnCrearHorario', function () {
                    let medico = $(this).data('medico');

                    $('#medico_id').val(medico.id);
                    $('#tituloModal').text(`Definir horario de ${medico.name} ${medico.Apellidos}`);

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

                $('.btnYaTieneHorario').on('click', function () {
                    alert('Este médico ya tiene un horario definido. Usa "Editar horario".');
                });

                

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
                            alert(xhr.responseJSON.message);
                        }
                    });
                });

                //+++ Editar horario +++

                $('#formEditarhorario').on('submit', function(e){
                    e.preventDefault();

                    let horarioId = $('#horario_id').val();

                    $.ajax({
                        url: '/horario/' + horarioId,
                        method: 'POST',   // ⬅️ SIEMPRE POST
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'PUT',
                            hora_inicio: $('#editar_hora_inicio').val(),
                            hora_fin: $('#editar_hora_fin').val(),
                            almuerzo_inicio: $('#editar_almuerzo_inicio').val(),
                            almuerzo_fin: $('#editar_almuerzo_fin').val(),
                            hora_atencion: $('#editar_hora_atencion').val()
                            
                        },
                        success: function () {
                            modalEditar.hide();
                            location.reload();
                        },
                        error: function (xhr) {
                            console.error(xhr.status);
                            console.error(xhr.responseText);
                            alert('Error al actualizar');
                        }
                    });
                });
            });
            
        </script>
    @endsection