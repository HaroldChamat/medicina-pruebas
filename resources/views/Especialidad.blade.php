@extends('layouts.app')

    @section('content')

        <header class="mb-4">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm px-4">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <a class="nav-link fw-semibold" href="/citas">Citas</a>
                    <a class="nav-link fw-semibold" href="/">Ir a inicio</a>

                    @if(session('admin') === 1 || session('cargo') === 'Medico' || session('cargo') === 'Paciente')
                        <a href="/logout" class="btn btn-outline-danger btn-sm">
                            Cerrar sesión
                        </a>
                    @endif
                </div>
            </nav>
        </header>

        <div class="container">

            <h4 class="fw-bold mb-4">Especialidades de Médicos</h4>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Médico</th>
                                    <th>Especialidad</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($medicos as $medico)
                                    <tr>
                                        <td>
                                            {{ $medico->name }} {{ $medico->Apellidos }}
                                        </td>

                                        <td>
                                            @if($medico->especialidad)
                                                <span class="badge bg-primary">
                                                    {{ $medico->especialidad->Nombre_especialidad }}
                                                </span>
                                            @else
                                                <span class="text-danger fw-semibold">
                                                    Sin especialidad
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if(!$medico->especialidad)
                                                <button
                                                    class="btn btn-success btn-sm btnAsignarEspecialidad"
                                                    data-medico='@json($medico)'>
                                                    Asignar especialidad
                                                </button>
                                            @else
                                                <button
                                                    class="btn btn-outline-primary btn-sm btnEditarEspecialidad"
                                                    data-medico='@json($medico)'>
                                                    Editar especialidad
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

        </div>

        {{-- Modal --}}
        <div class="modal fade" id="modalEspecialidad" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formEspecialidad">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="tituloEspecialidad"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="medico_id" id="medico_id">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Especialidad
                                </label>
                                <select
                                    name="especialidad_id"
                                    id="especialidad_id"
                                    class="form-select"
                                    required>
                                    <option value="" disabled selected>
                                        Seleccione especialidad
                                    </option>
                                    @foreach($especialidades as $especialidad)
                                        <option value="{{ $especialidad->id }}">
                                            {{ $especialidad->Nombre_especialidad }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection

    @section('javascript')
    @parent

        <script>
            $(document).ready(function () {

                let modalEspecialidad = new bootstrap.Modal(
                    document.getElementById('modalEspecialidad')
                );

                $(document).on('click', '.btnAsignarEspecialidad, .btnEditarEspecialidad', function () {
                    let medico = $(this).data('medico');

                    $('#medico_id').val(medico.id);
                    $('#tituloEspecialidad').text(
                        `Especialidad de ${medico.name} ${medico.Apellidos}`
                    );

                    $('#especialidad_id').val(medico.especialidad_id ?? '');
                    modalEspecialidad.show();
                });

                $('#formEspecialidad').on('submit', function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: '/usuario/especialidad',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            medico_id: $('#medico_id').val(),
                            especialidad_id: $('#especialidad_id').val()
                        },
                        success: function () {
                            modalEspecialidad.hide();
                            location.reload();
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON.message ?? 'Error al guardar');
                        }
                    });
                });
            });
        </script>
    @endsection