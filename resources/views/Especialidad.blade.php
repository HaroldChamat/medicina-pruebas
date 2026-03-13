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
        <button class="btn btn-success rounded-pill" id="btnNuevaEspecialidad">
            <i class="bi bi-plus-circle me-1"></i> Nueva especialidad
        </button>
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
                                            <span class="badge bg-primary me-1">
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
                                    {{ $esp->Nombre_especialidad }}
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
        $('#labelPrincipal').text($(this).data('nombre'));

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
            $('#listaPrincipal').find(`[data-id="${principalId}"]`).data('nombre') ?? ''
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