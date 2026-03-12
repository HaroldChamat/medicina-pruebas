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
            <div class="modal-content shadow">
                <form id="formEspecialidad">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="tituloEspecialidad"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="medico_id" id="medico_id">

                        <div id="step-1">
                            <label class="form-label fw-bold mb-3 text-primary">1. Seleccione la Especialidad Base</label>
                            <div class="list-group">
                                @foreach($especialidades as $especialidad)
                                    <button type="button" 
                                        class="list-group-item list-group-item-action select-main-espec" 
                                        data-id="{{ $especialidad->id }}"
                                        data-nombre="{{ $especialidad->Nombre_especialidad }}">
                                        {{ $especialidad->Nombre_especialidad }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div id="step-2" class="d-none">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0 text-success">2. Añada Especialidades Adicionales</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnBack">Cambiar Principal</button>
                            </div>
                            
                            <div class="row g-2">
                                @foreach($especialidades as $especialidad)
                                    <div class="col-6 espec-checkbox-item" id="item-{{ $especialidad->id }}" style="cursor: pointer;">
                                        <div class="border rounded p-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="especialidad_id[]" 
                                                    value="{{ $especialidad->id }}" 
                                                    id="check-{{ $especialidad->id }}">
                                                <label class="form-check-label" for="check-{{ $especialidad->id }}">
                                                    {{ $especialidad->Nombre_especialidad }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary d-none" id="btnSubmit">Guardar Cambios</button>
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
            const modalElement = document.getElementById('modalEspecialidad');
            const modalEspecialidad = new bootstrap.Modal(modalElement);
            
            // 1. Abrir Modal
            $(document).on('click', '.btnEditarEspecialidad', function () {
                const id = $(this).data('medico-id');
                const nombre = $(this).data('medico-nombre');
                const actuales = $(this).data('actuales') || []; 

                $('#medico_id').val(id);
                $('#tituloEspecialidad').text(`Gestionar especialidades de: ${nombre}`);

                // Reset visual
                $('#step-1').removeClass('d-none');
                $('#step-2').addClass('d-none');
                $('#btnSubmit').addClass('d-none');

                // Limpiar checkboxes
                $('input[name="especialidad_id[]"]').prop('checked', false);
                
                // Marcar actuales
                actuales.forEach(especId => {
                    $(`#check-${especId}`).prop('checked', true);
                });

                modalEspecialidad.show();
            });

            // 2. Paso 1: Seleccionar base
            $(document).on('click', '.select-main-espec', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                
                // Marcamos la principal
                $(`#check-${id}`).prop('checked', true);

                // Pasamos al paso 2
                $('#step-1').addClass('d-none');
                $('#step-2').removeClass('d-none');
                $('#btnSubmit').removeClass('d-none');
            });

            // 3. Paso 2: Lógica de Checkboxes (CORREGIDA)
            $(document).on('click', '.espec-checkbox-item', function(e) {
                // Detener que el clic se propague a otros elementos
                e.stopPropagation();
                
                const checkbox = $(this).find('input[type="checkbox"]');
                const isChecked = checkbox.prop('checked');
                const totalChecked = $('input[name="especialidad_id[]"]:checked').length;

                // Si ya hay 4 y queremos marcar uno nuevo, avisar
                if (!isChecked && totalChecked >= 4) {
                    alert('Solo puedes asignar un máximo de 4 especialidades.');
                    return;
                }

                // Cambiar el estado manualmente para asegurar que funcione
                checkbox.prop('checked', !isChecked);
            });

            // Evitar que el clic directo en el checkbox haga doble acción
            $(document).on('click', 'input[name="especialidad_id[]"]', function(e) {
                e.stopPropagation();
            });

            // 4. Botón Volver
            $('#btnBack').on('click', function() {
                $('#step-1').removeClass('d-none');
                $('#step-2').addClass('d-none');
                $('#btnSubmit').addClass('d-none');
            });

            // 5. Envío AJAX
            $('#formEspecialidad').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '/asignar-especialidad-medico',
                    method: 'POST', 
                    data: $(this).serialize(), 
                    success: function () {
                        modalEspecialidad.hide();
                        location.reload(); 
                    },
                    error: function () {
                        alert('Hubo un error al guardar las especialidades.');
                    }
                });
            });
        });
    </script>
    @endsection