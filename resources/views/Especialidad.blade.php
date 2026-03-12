@extends('layouts.app')

    @section('content')
        <div class="container">
            <div class="page-header">
                <h3 class="fw-bold">🩺 Especialidades de Médicos</h3>
                <p class="small mt-1">Gestión de especialidades asignadas a los médicos.</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th> {{-- Agregamos el contador --}}
                                    <th>Médico</th>
                                    <th>Especialidades</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($medicos as $medico)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td> {{-- Enumeración 1, 2, 3... --}}
                                        <td>
                                            {{ $medico->name }} {{ $medico->Apellidos }}
                                        </td>

                                        <td>
                                            {{-- Cambio clave: usamos el plural 'especialidades' e implode --}}
                                            @if($medico->especialidades && $medico->especialidades->count() > 0)
                                                <span class="badge bg-primary text-wrap">
                                                    {{ $medico->especialidades->pluck('Nombre_especialidad')->implode(', ') }}
                                                </span>
                                            @else
                                                <span class="text-danger fw-semibold italic">
                                                    Sin especialidad
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            {{-- Botón único para gestionar (Asignar o Editar) --}}
                                            <button
                                                class="btn btn-sm {{ $medico->especialidades->count() > 0 ? 'btn-outline-primary' : 'btn-success' }} btnEditarEspecialidad"
                                                data-medico-id="{{ $medico->id }}"
                                                data-medico-nombre="{{ $medico->name }} {{ $medico->Apellidos }}"
                                                {{-- Pasamos los IDs actuales como un array para el JS --}}
                                                data-actuales='@json($medico->especialidades->pluck("id"))'>
                                                <i class="bi bi-pencil-square"></i> 
                                                {{ $medico->especialidades->count() > 0 ? 'Editar' : 'Asignar' }} especialidad
                                            </button>
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
                                Seleccione Especialidades
                            </label>
                            {{-- Cambio clave: nombre con [], atributo 'multiple' y ID para el JS --}}
                            <select
                                name="especialidad_id[]"
                                id="especialidad_select"
                                class="form-select"
                                multiple
                                style="height: 200px;"
                                required>
                                @foreach($especialidades as $especialidad)
                                    <option value="{{ $especialidad->id }}">
                                        {{ $especialidad->Nombre_especialidad }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text mt-2 text-muted">
                                <i class="bi bi-info-circle"></i> Mantenga presionado <strong>Ctrl</strong> (o Cmd en Mac) para elegir varias.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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
                // 1. Inicializamos el modal una sola vez
                const modalElement = document.getElementById('modalEspecialidad');
                const modalEspecialidad = new bootstrap.Modal(modalElement);
                const selectEspecialidad = document.getElementById('especialidad_select');

                // 2. Evento para abrir el modal
                $(document).on('click', '.btnAsignarEspecialidad, .btnEditarEspecialidad', function () {
                    const id = $(this).data('medico-id');
                    const nombre = $(this).data('medico-nombre');
                    const actuales = $(this).data('actuales') || []; 

                    $('#medico_id').val(id);
                    $('#tituloEspecialidad').text(`Gestionar especialidades de: ${nombre}`);

                    Array.from(selectEspecialidad.options).forEach(option => {
                        option.selected = actuales.includes(parseInt(option.value));
                    });

                    modalEspecialidad.show();
                });

                // 3. Validación de máximo 4 especialidades
                $(selectEspecialidad).on('change', function() {
                    const seleccionadas = $(this).val();
                    if (seleccionadas && seleccionadas.length > 4) {
                        alert('Solo puedes asignar un máximo de 4 especialidades.');
                        $(this).find('option:selected').last().prop('selected', false);
                    }
                });

                // 4. Envío del formulario por AJAX con la URL ÚNICA
                $('#formEspecialidad').on('submit', function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: '/asignar-especialidad-medico', // URL actualizada para evitar conflictos
                        method: 'POST', 
                        data: $(this).serialize(), 
                        success: function (response) {
                            modalEspecialidad.hide();
                            location.reload(); 
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errores = xhr.responseJSON.errors;
                                let mensaje = "Errores encontrados:\n";
                                $.each(errores, function(campo, mensajes) {
                                    mensaje += "- " + mensajes.join(", ") + "\n";
                                });
                                alert(mensaje);
                            } else {
                                alert('Error crítico: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                            }
                            console.log("Error completo:", xhr.responseJSON);
                        }
                    });
                });
            });
        </script>
    @endsection