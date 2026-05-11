@extends('layouts.app')
@section('content')

<div class="container">
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">🩺 Especialidades de Médicos</h3>
            <p class="small mb-0" style="color: rgba(255,255,255,0.75);">
                Gestión de especialidades asignadas a los médicos.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-warning rounded-pill" id="btnGestionarEspecialidades">
                <i class="bi bi-pencil-square me-1"></i> Gestionar especialidades
            </button>
            <button class="btn btn-success rounded-pill" id="btnNuevaEspecialidad">
                <i class="bi bi-plus-circle me-1"></i> Nueva especialidad
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead style="background-color: #0d3b6e; color: white;">
                        <tr>
                            <th class="px-4 py-3" style="width: 50px;">#</th>
                            <th>Médico</th>
                            <th>Especialidades</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicos as $medico)
                            <tr>
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $medico->name }} {{ $medico->Apellidos }}
                                    </div>
                                    <small class="text-muted">{{ $medico->email }}</small>
                                </td>
                                <td>
                                    @if($medico->especialidades && $medico->especialidades->count() > 0)
                                        @foreach($medico->especialidades as $esp)
                                        <span class="badge bg-primary me-1" data-esp-id="{{ $esp->id }}">
                                            {{ $esp->Nombre_especialidad }}
                                        </span>
                                    @endforeach
                                    @else
                                        <span class="text-danger fw-semibold small">Sin especialidad</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm rounded-pill btnEditarEspecialidad
                                        {{ $medico->especialidades->count() > 0 ? 'btn-outline-primary' : 'btn-success' }}"
                                        data-medico-id="{{ $medico->id }}"
                                        data-medico-nombre="{{ $medico->name }} {{ $medico->Apellidos }}"
                                        data-actuales='@json($medico->especialidades->pluck("id"))'>
                                        <i class="bi bi-pencil-square me-1"></i>
                                        {{ $medico->especialidades->count() > 0 ? 'Editar' : 'Asignar' }}
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

{{-- ===== MODAL ASIGNAR/EDITAR ESPECIALIDAD ===== --}}
<div class="modal fade" id="modalEspecialidad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <form id="formEspecialidad">
                @csrf
                @method('PUT')
                <input type="hidden" name="medico_id" id="medico_id">
                <input type="hidden" name="especialidad_principal_id" id="especialidad_principal_id">

                <div class="modal-header text-white" style="background-color: #0d3b6e;">
                    <h5 class="modal-title fw-bold" id="tituloEspecialidad"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- PASO 1: Especialidad principal --}}
                    <div id="step-1">
                        <p class="fw-semibold text-primary mb-3">
                            <i class="bi bi-1-circle-fill me-1"></i>
                            Selecciona la especialidad principal
                        </p>
                        <div class="list-group" id="listaPrincipal">
                            @foreach($especialidades as $esp)
                                <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center select-main-espec"
                                    data-id="{{ $esp->id }}"
                                    data-nombre="{{ $esp->Nombre_especialidad }}">
                                    <span class="esp-nombre-label">{{ $esp->Nombre_especialidad }}</span>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- PASO 2: Especialidades adicionales --}}
                    <div id="step-2" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <p class="fw-semibold text-success mb-0">
                                <i class="bi bi-2-circle-fill me-1"></i>
                                Especialidades adicionales
                                <span class="badge bg-secondary ms-1" id="contadorEsp">0/3</span>
                            </p>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="btnBack">
                                <i class="bi bi-arrow-left me-1"></i> Cambiar principal
                            </button>
                        </div>

                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Principal: <strong id="labelPrincipal"></strong>
                            — puedes agregar hasta <strong>3</strong> adicionales
                        </div>

                        {{-- Checkbox adicionales (sin mostrar la principal) --}}
                        <div class="row g-2" id="listaAdicionales">
                            @foreach($especialidades as $esp)
                                <div class="col-6 espec-item" data-id="{{ $esp->id }}">
                                    <div class="border rounded p-2 d-flex align-items-center gap-2"
                                         style="cursor: pointer;">
                                        <input class="form-check-input m-0 flex-shrink-0"
                                               type="checkbox"
                                               name="especialidad_id[]"
                                               value="{{ $esp->id }}"
                                               id="check-{{ $esp->id }}">
                                        <label class="form-check-label small mb-0 w-100"
                                               for="check-{{ $esp->id }}"
                                               style="cursor: pointer;">
                                            {{ $esp->Nombre_especialidad }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-muted small mt-2 mb-0" id="msgMaximo" style="display:none;">
                            <i class="bi bi-exclamation-circle text-warning me-1"></i>
                            Has alcanzado el máximo de 3 especialidades adicionales.
        				</p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill"
                            data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary rounded-pill d-none" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL GESTIONAR ESPECIALIDADES ===== --}}
<div class="modal fade" id="modalGestionarEsp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i> Gestionar Especialidades
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4 py-3">Especialidad</th>
                            <th class="text-center py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaGestionEsp">
                        @foreach($especialidades as $esp)
                            <tr id="fila-esp-{{ $esp->id }}">
                                <td class="px-4">
                                    <span id="nombre-esp-{{ $esp->id }}">{{ $esp->Nombre_especialidad }}</span>
                                    <input type="text" id="input-esp-{{ $esp->id }}"
                                           class="form-control form-control-sm d-none"
                                           value="{{ $esp->Nombre_especialidad }}">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill me-1 btnEditarEsp"
                                            data-id="{{ $esp->id }}">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-success rounded-pill me-1 btnGuardarEsp d-none"
                                            data-id="{{ $esp->id }}">
                                        <i class="bi bi-save me-1"></i> Guardar
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 btnCancelarEsp d-none"
                                            data-id="{{ $esp->id }}">
                                        <i class="bi bi-x me-1"></i> Cancelar
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill btnEliminarEsp"
                                            data-id="{{ $esp->id }}"
                                            data-nombre="{{ $esp->Nombre_especialidad }}">
                                        <i class="bi bi-trash me-1"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill"
                        data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL CONFIRMAR ELIMINAR ESPECIALIDAD ===== --}}
<div class="modal fade" id="modalConfirmarEliminarEsp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i> Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de eliminar la especialidad <strong id="nombreEspEliminar"></strong>?</p>
                <div class="alert alert-warning small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Se eliminará de todos los médicos que la tengan asignada.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger rounded-pill" id="btnConfirmarEliminarEsp">
                    <i class="bi bi-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ===== MODAL NUEVA ESPECIALIDAD ===== --}}
<div class="modal fade" id="modalNuevaEsp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i> Nueva Especialidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">Nombre de la especialidad</label>
                <input type="text" id="inputNuevaEsp" class="form-control"
                       placeholder="Ej: Neurología, Dermatología...">
                <div id="errorNuevaEsp" class="text-danger small mt-1 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill"
                        data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success rounded-pill" id="btnGuardarNuevaEsp">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
@parent
<script>
$(document).ready(function () {

    const modalEsp     = new bootstrap.Modal(document.getElementById('modalEspecialidad'));
    const modalNuevaEsp = new bootstrap.Modal(document.getElementById('modalNuevaEsp'));
    let principalId    = null;

    // ─── ABRIR MODAL ASIGNAR/EDITAR ──────────────────────────────────
    $(document).on('click', '.btnEditarEspecialidad', function () {
        const actuales = $(this).data('actuales') || [];

        $('#medico_id').val($(this).data('medico-id'));
        $('#tituloEspecialidad').text('Especialidades: ' + $(this).data('medico-nombre'));

        // Reset
        principalId = null;
        $('#especialidad_principal_id').val('');
        $('#step-1').removeClass('d-none');
        $('#step-2').addClass('d-none');
        $('#btnSubmit').addClass('d-none');
        $('input[name="especialidad_id[]"]').prop('checked', false);
        $('.select-main-espec').removeClass('active');

        // Si ya tiene especialidades, pre-seleccionar la primera como principal
        if (actuales.length > 0) {
            principalId = actuales[0];
            $('#especialidad_principal_id').val(principalId);
            mostrarPaso2(principalId, actuales);
        }

        modalEsp.show();
    });

    // ─── PASO 1: elegir principal ────────────────────────────────────
    $(document).on('click', '.select-main-espec', function () {
        principalId = $(this).data('id');
        $('#especialidad_principal_id').val(principalId);
        $('#labelPrincipal').text($(this).attr('data-nombre'));

        // Marcar visualmente
        $('.select-main-espec').removeClass('active');
        $(this).addClass('active');

        mostrarPaso2(principalId, [principalId]);
    });

    function mostrarPaso2(principalId, seleccionadas) {
        // Ocultar la principal en el listado de adicionales
        $('.espec-item').show();
        $(`.espec-item[data-id="${principalId}"]`).hide();

        // Desmarcar todo y marcar las adicionales (sin la principal)
        $('input[name="especialidad_id[]"]').prop('checked', false);
        seleccionadas.forEach(id => {
            if (id != principalId) {
                $(`#check-${id}`).prop('checked', true);
            }
        });

        actualizarContador();

        $('#step-1').addClass('d-none');
        $('#step-2').removeClass('d-none');
        $('#btnSubmit').removeClass('d-none');
        $('#labelPrincipal').text(
            $('#listaPrincipal').find(`[data-id="${principalId}"]`).attr('data-nombre') ?? ''
        );
    }

    // ─── PASO 2: checkboxes adicionales ─────────────────────────────
    $(document).on('change', 'input[name="especialidad_id[]"]', function () {
        const totalChecked = $('input[name="especialidad_id[]"]:checked').length;

        if ($(this).prop('checked') && totalChecked > 3) {
            $(this).prop('checked', false);
            $('#msgMaximo').show();
            return;
        }

        $('#msgMaximo').hide();
        actualizarContador();
    });

    function actualizarContador() {
        const total = $('input[name="especialidad_id[]"]:checked').length;
        $('#contadorEsp').text(total + '/3');
        $('#contadorEsp').removeClass('bg-secondary bg-warning bg-danger')
            .addClass(total >= 3 ? 'bg-danger' : total >= 2 ? 'bg-warning' : 'bg-secondary');
    }

    // ─── VOLVER AL PASO 1 ────────────────────────────────────────────
    $('#btnBack').on('click', function () {
        $('#step-1').removeClass('d-none');
        $('#step-2').addClass('d-none');
        $('#btnSubmit').addClass('d-none');
        $('#msgMaximo').hide();
    });

    // ─── ENVIAR FORMULARIO ───────────────────────────────────────────
    $('#formEspecialidad').on('submit', function (e) {
        e.preventDefault();

        // Construir array final: principal + adicionales
        const adicionales = $('input[name="especialidad_id[]"]:checked').map(function () {
            return $(this).val();
        }).get();

        const todos = [principalId.toString(), ...adicionales];

        $.ajax({
            url: '/asignar-especialidad-medico',
            method: 'POST',
            data: {
                _token:           $('meta[name="csrf-token"]').attr('content'),
                _method:          'PUT',
                medico_id:        $('#medico_id').val(),
                'especialidad_id[]': todos,
            },
            success: function () {
                mostrarToast('Especialidades actualizadas', 'success');
                agregarNotificacion('Especialidades de médico actualizadas', 'info');
                setTimeout(() => { modalEsp.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            }
        });
    });


    // ─── GESTIONAR ESPECIALIDADES ────────────────────────────────────────
    $('#btnGestionarEspecialidades').on('click', function () {
        new bootstrap.Modal(document.getElementById('modalGestionarEsp')).show();
    });

    let espIdEliminar = null;

    // Editar nombre
    $(document).on('click', '.btnEditarEsp', function () {
        const id = $(this).data('id');
        $(`#nombre-esp-${id}`).addClass('d-none');
        $(`#input-esp-${id}`).removeClass('d-none').focus();
        $(this).addClass('d-none');
        $(`#fila-esp-${id} .btnGuardarEsp`).removeClass('d-none');
        $(`#fila-esp-${id} .btnCancelarEsp`).removeClass('d-none');
        $(`#fila-esp-${id} .btnEliminarEsp`).addClass('d-none');
    });

    // Cancelar edición
    $(document).on('click', '.btnCancelarEsp', function () {
        const id = $(this).data('id');
        $(`#nombre-esp-${id}`).removeClass('d-none');
        $(`#input-esp-${id}`).addClass('d-none');
        $(`#fila-esp-${id} .btnEditarEsp`).removeClass('d-none');
        $(this).addClass('d-none');
        $(`#fila-esp-${id} .btnGuardarEsp`).addClass('d-none');
        $(`#fila-esp-${id} .btnEliminarEsp`).removeClass('d-none');
    });

    // Guardar nombre editado
    $(document).on('click', '.btnGuardarEsp', function () {
        const id = $(this).data('id');
        const nuevoNombre = $(`#input-esp-${id}`).val().trim();

        if (!nuevoNombre) {
            mostrarToast('El nombre no puede estar vacío', 'warning');
            return;
        }

        $.ajax({
            url: '/especialidad/' + id,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
                Nombre_especialidad: nuevoNombre,
            },
            success: function (res) {
                const nombreFinal = res.nombre;

                // Actualizar texto visible y valor del input
                $(`#nombre-esp-${id}`).text(nombreFinal).removeClass('d-none');
                $(`#input-esp-${id}`).val(nombreFinal).addClass('d-none');

                // Actualizar data-nombre del botón eliminar
                $(`#fila-esp-${id} .btnEliminarEsp`).attr('data-nombre', nombreFinal);

                // Restaurar botones
                $(`#fila-esp-${id} .btnEditarEsp`).removeClass('d-none');
                $(`#fila-esp-${id} .btnGuardarEsp`).addClass('d-none');
                $(`#fila-esp-${id} .btnCancelarEsp`).addClass('d-none');
                $(`#fila-esp-${id} .btnEliminarEsp`).removeClass('d-none');

                // Actualizar todos los badges en la tabla de médicos que tengan este id
                $(`.badge[data-esp-id="${id}"]`).text(nombreFinal);

                // Actualizar texto y data-nombre en el botón de la lista principal
                const $btnPrincipal = $(`.select-main-espec[data-id="${id}"]`);
                $btnPrincipal.attr('data-nombre', nombreFinal);
                $btnPrincipal.find('.esp-nombre-label').text(nombreFinal);

                // Actualizar label del checkbox adicional
                $(`label[for="check-${id}"]`).text(nombreFinal);

                // Si esta especialidad es la principal actualmente seleccionada, actualizar el label
                if ($('#especialidad_principal_id').val() == id) {
                    $('#labelPrincipal').text(nombreFinal);
                }

                // Actualizar también dentro del alert del paso 2 (el strong con el nombre)
                const $labelAlert = $('#labelPrincipal');
                if ($labelAlert.text() === $btnPrincipal.attr('data-nombre') || 
                    $('#especialidad_principal_id').val() == id) {
                    $labelAlert.text(nombreFinal);
                }

                mostrarToast('Especialidad actualizada correctamente', 'success');
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al actualizar', 'danger');
            }
        });
    });

    // Abrir modal eliminar
    $(document).on('click', '.btnEliminarEsp', function () {
        espIdEliminar = $(this).data('id');
        $('#nombreEspEliminar').text($(this).data('nombre'));
        new bootstrap.Modal(document.getElementById('modalConfirmarEliminarEsp')).show();
    });

    // Confirmar eliminar
    $('#btnConfirmarEliminarEsp').on('click', function () {
        $.ajax({
            url: '/especialidad/' + espIdEliminar,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE',
            },
            success: function () {
                $(`#fila-esp-${espIdEliminar}`).remove();
                bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminarEsp')).hide();
                mostrarToast('Especialidad eliminada correctamente', 'success');
                espIdEliminar = null;
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al eliminar', 'danger');
            }
        });
    });

    

    // ─── NUEVA ESPECIALIDAD ──────────────────────────────────────────
    $('#btnNuevaEspecialidad').on('click', function () {
        $('#inputNuevaEsp').val('');
        $('#errorNuevaEsp').addClass('d-none').text('');
        modalNuevaEsp.show();
    });

    $('#btnGuardarNuevaEsp').on('click', function () {
        const nombre = $('#inputNuevaEsp').val().trim();

        if (!nombre) {
            $('#errorNuevaEsp').removeClass('d-none').text('El nombre es obligatorio.');
            return;
        }

        $.ajax({
            url: '/especialidad',
            method: 'POST',
            data: {
                _token:               $('meta[name="csrf-token"]').attr('content'),
                Nombre_especialidad:  nombre,
            },
            success: function (resp) {
                mostrarToast('Especialidad creada correctamente', 'success');
                agregarNotificacion('Nueva especialidad: ' + resp.nombre, 'success');

                // Agregar a ambas listas sin recargar
                const itemLista = `
                    <button type="button"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center select-main-espec"
                        data-id="${resp.id}" data-nombre="${resp.nombre}">
                        ${resp.nombre}
                        <i class="bi bi-chevron-right text-muted"></i>
                    </button>`;

                const itemCheck = `
                    <div class="col-6 espec-item" data-id="${resp.id}">
                        <div class="border rounded p-2 d-flex align-items-center gap-2" style="cursor: pointer;">
                            <input class="form-check-input m-0 flex-shrink-0"
                                   type="checkbox" name="especialidad_id[]"
                                   value="${resp.id}" id="check-${resp.id}">
                            <label class="form-check-label small mb-0 w-100"
                                   for="check-${resp.id}" style="cursor: pointer;">
                                ${resp.nombre}
                            </label>
                        </div>
                    </div>`;

                $('#listaPrincipal').append(itemLista);
                $('#listaAdicionales').append(itemCheck);

                setTimeout(() => { modalNuevaEsp.hide(); location.reload(); }, 1500);
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.errors?.Nombre_especialidad?.[0]
                    ?? xhr.responseJSON?.message
                    ?? 'Error al crear la especialidad';
                $('#errorNuevaEsp').removeClass('d-none').text(msg);
            }
        });
    });

});
</script>
@endsection