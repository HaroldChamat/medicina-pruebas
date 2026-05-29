@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard2-pulse-fill me-2"></i> Prestaciones Médicas
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.75);">
                Gestión de prestaciones y asignación a médicos.
            </p>
        </div>
        <button class="btn btn-success rounded-pill" id="btnNuevaPrestacion">
            <i class="bi bi-plus-circle me-1"></i> Nueva prestación
        </button>
    </div>

    {{-- ── CATÁLOGO DE PRESTACIONES ── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-semibold text-white" style="background-color: #0d3b6e;">
            <i class="bi bi-list-check me-1"></i> Catálogo de Prestaciones
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="px-4">#</th>
                            <th>Nombre</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prestaciones as $p)
                            <tr id="fila-prest-{{ $p->id }}">
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td>
                                    <span id="nombre-prest-{{ $p->id }}">{{ $p->nombre }}</span>
                                    <input type="text" id="input-prest-{{ $p->id }}"
                                           class="form-control form-control-sm d-none"
                                           value="{{ $p->nombre }}">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill me-1 btnEditarPrest"
                                            data-id="{{ $p->id }}">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-success rounded-pill me-1 btnGuardarPrest d-none"
                                            data-id="{{ $p->id }}">
                                        <i class="bi bi-save me-1"></i> Guardar
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 btnCancelarPrest d-none"
                                            data-id="{{ $p->id }}">
                                        <i class="bi bi-x me-1"></i> Cancelar
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill btnEliminarPrest"
                                            data-id="{{ $p->id }}"
                                            data-nombre="{{ $p->nombre }}">
                                        <i class="bi bi-trash me-1"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── PRESTACIONES POR MÉDICO ── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-semibold text-white" style="background-color: #0d3b6e;">
            <i class="bi bi-person-badge me-1"></i> Prestaciones por Médico
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="px-4">Médico</th>
                            <th>Prestaciones asignadas</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicos as $medico)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold">{{ $medico->name }} {{ $medico->Apellidos }}</div>
                                    <small class="text-muted">{{ $medico->email }}</small>
                                </td>
                                <td>
                                    @forelse($medico->medicoPrestaciones as $mp)
                                        <span class="badge bg-primary me-1 mb-1">
                                            {{ $mp->prestacion->nombre }}
                                            <small class="opacity-75">
                                                ({{ \Carbon\Carbon::createFromFormat('H:i:s', $mp->hora_entrada)->format('H:i') }}
                                                –
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $mp->hora_salida)->format('H:i') }},
                                                {{ $mp->hora_atencion }} min,
                                                {{ $mp->cant_online }} online)
                                            </small>
                                        </span>
                                    @empty
                                        <span class="text-muted small fst-italic">Sin prestaciones asignadas</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill btnAsignarPrestaciones"
                                            data-medico-id="{{ $medico->id }}"
                                            data-medico-nombre="{{ $medico->name }} {{ $medico->Apellidos }}"
                                            data-prestaciones='@json($medico->medicoPrestaciones)'>
                                        <i class="bi bi-pencil-square me-1"></i> Gestionar
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

{{-- ── MODAL NUEVA PRESTACIÓN ── --}}
<div class="modal fade" id="modalNuevaPrest" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i> Nueva Prestación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" id="inputNuevaPrest" class="form-control"
                       placeholder="Ej: Consulta Médica, Kinesiología...">
                <div id="errorNuevaPrest" class="text-danger small mt-1 d-none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success rounded-pill" id="btnGuardarNuevaPrest">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL CONFIRMAR ELIMINAR PRESTACIÓN ── --}}
<div class="modal fade" id="modalEliminarPrest" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i> Eliminar Prestación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Eliminar la prestación <strong id="nombrePrestEliminar"></strong>?</p>
                <div class="alert alert-warning small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Se desasociará de todos los médicos que la tengan asignada.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger rounded-pill" id="btnConfirmarEliminarPrest">
                    <i class="bi bi-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL ASIGNAR PRESTACIONES A MÉDICO ── --}}
<div class="modal fade" id="modalAsignarPrest" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold" id="tituloAsignar">
                    <i class="bi bi-clipboard2-plus me-2"></i> Gestionar Prestaciones
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="asignar_medico_id">

                <div class="alert alert-info small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Define cada prestación que atiende este médico con su horario específico y cupos online.
                    Las horas de entrada/salida definen el rango en que se atiende esa prestación.
                </div>

                <div id="listaPrestacionesAsignar">
                    {{-- Se llena dinámicamente --}}
                </div>

                <button class="btn btn-outline-primary btn-sm rounded-pill mt-2" id="btnAgregarPrestRow">
                    <i class="bi bi-plus-circle me-1"></i> Agregar prestación
                </button>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary rounded-pill" id="btnGuardarAsignacion">
                    <i class="bi bi-save me-1"></i> Guardar cambios
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
    const csrf = $('meta[name="csrf-token"]').attr('content');
    let prestIdEliminar = null;
    const prestaciones  = @json($prestaciones);

    const modalNueva   = new bootstrap.Modal(document.getElementById('modalNuevaPrest'));
    const modalElim    = new bootstrap.Modal(document.getElementById('modalEliminarPrest'));
    const modalAsignar = new bootstrap.Modal(document.getElementById('modalAsignarPrest'));

    // ── NUEVA PRESTACIÓN ─────────────────────────────────────────────────
    $('#btnNuevaPrestacion').on('click', function () {
        $('#inputNuevaPrest').val('');
        $('#errorNuevaPrest').addClass('d-none').text('');
        modalNueva.show();
    });

    $('#btnGuardarNuevaPrest').on('click', function () {
        const nombre = $('#inputNuevaPrest').val().trim();
        if (!nombre) {
            $('#errorNuevaPrest').removeClass('d-none').text('El nombre es obligatorio.');
            return;
        }
        $.ajax({
            url: '/admin/prestaciones',
            method: 'POST',
            data: { _token: csrf, nombre },
            success: function () {
                mostrarToast('Prestación creada correctamente', 'success');
                modalNueva.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.errors?.nombre?.[0]
                    ?? xhr.responseJSON?.message
                    ?? 'Error al crear la prestación';
                $('#errorNuevaPrest').removeClass('d-none').text(msg);
            }
        });
    });

    // ── EDITAR NOMBRE PRESTACIÓN ─────────────────────────────────────────
    $(document).on('click', '.btnEditarPrest', function () {
        const id = $(this).data('id');
        $(`#nombre-prest-${id}`).addClass('d-none');
        $(`#input-prest-${id}`).removeClass('d-none').focus();
        $(this).addClass('d-none');
        $(`#fila-prest-${id} .btnGuardarPrest`).removeClass('d-none');
        $(`#fila-prest-${id} .btnCancelarPrest`).removeClass('d-none');
        $(`#fila-prest-${id} .btnEliminarPrest`).addClass('d-none');
    });

    $(document).on('click', '.btnCancelarPrest', function () {
        const id = $(this).data('id');
        $(`#nombre-prest-${id}`).removeClass('d-none');
        $(`#input-prest-${id}`).addClass('d-none');
        $(`#fila-prest-${id} .btnEditarPrest`).removeClass('d-none');
        $(this).addClass('d-none');
        $(`#fila-prest-${id} .btnGuardarPrest`).addClass('d-none');
        $(`#fila-prest-${id} .btnEliminarPrest`).removeClass('d-none');
    });

    $(document).on('click', '.btnGuardarPrest', function () {
        const id     = $(this).data('id');
        const nombre = $(`#input-prest-${id}`).val().trim();
        if (!nombre) { mostrarToast('El nombre no puede estar vacío', 'warning'); return; }

        $.ajax({
            url: '/admin/prestaciones/' + id,
            method: 'POST',
            data: { _token: csrf, _method: 'PUT', nombre },
            success: function (res) {
                $(`#nombre-prest-${id}`).text(res.nombre).removeClass('d-none');
                $(`#input-prest-${id}`).addClass('d-none');
                $(`#fila-prest-${id} .btnEditarPrest`).removeClass('d-none');
                $(`#fila-prest-${id} .btnGuardarPrest, #fila-prest-${id} .btnCancelarPrest`).addClass('d-none');
                $(`#fila-prest-${id} .btnEliminarPrest`).removeClass('d-none');
                mostrarToast('Prestación actualizada', 'success');
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al actualizar', 'danger');
            }
        });
    });

    // ── ELIMINAR PRESTACIÓN ──────────────────────────────────────────────
    $(document).on('click', '.btnEliminarPrest', function () {
        prestIdEliminar = $(this).data('id');
        $('#nombrePrestEliminar').text($(this).data('nombre'));
        modalElim.show();
    });

    $('#btnConfirmarEliminarPrest').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: '/admin/prestaciones/' + prestIdEliminar,
            method: 'POST',
            data: { _token: csrf, _method: 'DELETE' },
            success: function () {
                $(`#fila-prest-${prestIdEliminar}`).remove();
                modalElim.hide();
                mostrarToast('Prestación eliminada correctamente', 'warning');
                prestIdEliminar = null;
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al eliminar', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    // ── ASIGNAR / GESTIONAR PRESTACIONES DE UN MÉDICO ───────────────────
    $(document).on('click', '.btnAsignarPrestaciones', function () {
        const medicoId     = $(this).data('medico-id');
        const medicoNombre = $(this).data('medico-nombre');
        const actuales     = $(this).data('prestaciones') || [];

        $('#asignar_medico_id').val(medicoId);
        $('#tituloAsignar').html(
            `<i class="bi bi-clipboard2-plus me-2"></i>Prestaciones: ${medicoNombre}`
        );

        $('#listaPrestacionesAsignar').empty();
        if (actuales.length > 0) {
            actuales.forEach(mp => agregarFilaPrestacion(mp));
        }

        modalAsignar.show();
    });

    function agregarFilaPrestacion(mp = null) {
        const opciones = prestaciones.map(p =>
            `<option value="${p.id}" ${mp && mp.id_prestacion == p.id ? 'selected' : ''}>
                ${p.nombre}
             </option>`
        ).join('');

        const fila = `
        <div class="border rounded p-3 mb-3 prest-row">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Prestación</label>
                    <select class="form-select form-select-sm sel-prestacion">
                        <option value="">Seleccione...</option>
                        ${opciones}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        <i class="bi bi-clock me-1"></i>Hora entrada
                    </label>
                    <input type="time" class="form-control form-control-sm inp-entrada"
                           value="${mp ? mp.hora_entrada.substring(0,5) : ''}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        <i class="bi bi-clock me-1"></i>Hora salida
                    </label>
                    <input type="time" class="form-control form-control-sm inp-salida"
                           value="${mp ? mp.hora_salida.substring(0,5) : ''}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Min/paciente</label>
                    <input type="number" class="form-control form-control-sm inp-atencion"
                           min="5" max="120" placeholder="30"
                           value="${mp ? mp.hora_atencion : 30}">
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-semibold">Horas</label>
                    <input type="number" class="form-control form-control-sm inp-horas"
                           min="1" max="24" value="${mp ? mp.cantidad_horas : 1}">
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-semibold">Online</label>
                    <input type="number" class="form-control form-control-sm inp-online"
                           min="0" value="${mp ? mp.cant_online : 0}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill w-100 btnQuitarPrestRow"
                            title="Quitar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Los slots disponibles se calculan automáticamente entre hora entrada y salida
                        según los minutos por paciente.
                    </small>
                </div>
            </div>
        </div>`;

        $('#listaPrestacionesAsignar').append(fila);
    }

    $('#btnAgregarPrestRow').on('click', () => agregarFilaPrestacion());

    $(document).on('click', '.btnQuitarPrestRow', function () {
        $(this).closest('.prest-row').remove();
    });

    $('#btnGuardarAsignacion').on('click', function () {
        const medicoId = $('#asignar_medico_id').val();
        const filas    = [];
        let valido     = true;

        $('.prest-row').each(function () {
            const idPrest  = $(this).find('.sel-prestacion').val();
            const entrada  = $(this).find('.inp-entrada').val();
            const salida   = $(this).find('.inp-salida').val();
            const atencion = $(this).find('.inp-atencion').val();
            const horas    = $(this).find('.inp-horas').val();
            const online   = $(this).find('.inp-online').val();

            if (!idPrest || !entrada || !salida || !atencion || !horas) {
                valido = false;
                return false;
            }

            if (salida <= entrada) {
                mostrarToast('La hora de salida debe ser posterior a la de entrada', 'warning');
                valido = false;
                return false;
            }

            filas.push({
                id_prestacion:  idPrest,
                hora_entrada:   entrada,
                hora_salida:    salida,
                hora_atencion:  parseInt(atencion),
                cantidad_horas: parseInt(horas),
                cant_online:    parseInt(online) || 0,
            });
        });

        if (!valido) {
            if (filas.length === 0 && $('.prest-row').length > 0) return;
            return;
        }

        if (filas.length === 0 && $('.prest-row').length === 0) {
            // Sin filas: eliminar todas las prestaciones del médico
            if (!confirm('¿Quitar todas las prestaciones de este médico?')) return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url: '/admin/prestaciones/asignar-medico',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                _token:       csrf,
                medico_id:    medicoId,
                prestaciones: filas,
            }),
            success: function () {
                mostrarToast('Prestaciones guardadas correctamente', 'success');
                modalAsignar.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar cambios');
            }
        });
    });
});
</script>
@endsection