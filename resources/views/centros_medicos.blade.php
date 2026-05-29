@extends('layouts.app')
@section('content')

<div class="container mt-4">

    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-building-fill me-2"></i> Centros Médicos
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.75);">
                Gestión de centros médicos del sistema.
            </p>
        </div>
        <button class="btn btn-success rounded-pill" id="btnNuevoCentro">
            <i class="bi bi-plus-circle me-1"></i> Nuevo centro
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #0d3b6e; color: white;">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Médicos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCentros">
                        @foreach($centros as $centro)
                            <tr id="fila-centro-{{ $centro->id }}">
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $centro->nombre }}</td>
                                <td>{{ $centro->direccion }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $centro->users_count }} usuario(s)</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill btnEditarCentro"
                                            data-id="{{ $centro->id }}"
                                            data-nombre="{{ $centro->nombre }}"
                                            data-direccion="{{ $centro->direccion }}">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill btnEliminarCentro"
                                            data-id="{{ $centro->id }}"
                                            data-nombre="{{ $centro->nombre }}">
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
</div>

{{-- Modal Crear/Editar --}}
<div class="modal fade" id="modalCentro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0d3b6e;">
                <h5 class="modal-title fw-bold" id="tituloCentro">
                    <i class="bi bi-building me-2"></i> Nuevo Centro Médico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="centro_id">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" id="centro_nombre" class="form-control"
                           placeholder="Ej: Clínica Central">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Dirección</label>
                    <input type="text" id="centro_direccion" class="form-control"
                           placeholder="Ej: Av. Principal 1234, Santiago">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success rounded-pill" id="btnGuardarCentro">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmar Eliminar --}}
<div class="modal fade" id="modalEliminarCentro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i> Eliminar Centro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Eliminar el centro <strong id="nombreCentroEliminar"></strong>?
                Los médicos asociados quedarán sin centro asignado.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger rounded-pill" id="btnConfirmarEliminarCentro">
                    <i class="bi bi-trash me-1"></i> Sí, eliminar
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
    let centroIdEliminar = null;
    let modoEditar = false;

    const modalCentro   = new bootstrap.Modal(document.getElementById('modalCentro'));
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminarCentro'));

    // ── ABRIR MODAL CREAR ────────────────────────────────────────────
    $('#btnNuevoCentro').on('click', function () {
        modoEditar = false;
        $('#tituloCentro').html('<i class="bi bi-building me-2"></i> Nuevo Centro Médico');
        $('#centro_id').val('');
        $('#centro_nombre').val('');
        $('#centro_direccion').val('');
        modalCentro.show();
    });

    // ── ABRIR MODAL EDITAR ───────────────────────────────────────────
    $(document).on('click', '.btnEditarCentro', function () {
        modoEditar = true;
        $('#tituloCentro').html('<i class="bi bi-pencil me-2"></i> Editar Centro Médico');
        $('#centro_id').val($(this).data('id'));
        $('#centro_nombre').val($(this).data('nombre'));
        $('#centro_direccion').val($(this).data('direccion'));
        modalCentro.show();
    });

    // ── GUARDAR ──────────────────────────────────────────────────────
    $('#btnGuardarCentro').on('click', function () {
        const nombre    = $('#centro_nombre').val().trim();
        const direccion = $('#centro_direccion').val().trim();
        if (!nombre || !direccion) {
            mostrarToast('Completa todos los campos', 'warning');
            return;
        }

        const id  = $('#centro_id').val();
        const url = modoEditar
            ? '/admin/centros-medicos/' + id
            : '/admin/centros-medicos';

        $.ajax({
            url,
            method: 'POST',
            data: {
                _token: csrf,
                _method: modoEditar ? 'PUT' : 'POST',
                nombre, direccion,
            },
            success: function () {
                mostrarToast('Centro guardado correctamente', 'success');
                modalCentro.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            }
        });
    });

    // ── ELIMINAR ─────────────────────────────────────────────────────
    $(document).on('click', '.btnEliminarCentro', function () {
        centroIdEliminar = $(this).data('id');
        $('#nombreCentroEliminar').text($(this).data('nombre'));
        modalEliminar.show();
    });

    $('#btnConfirmarEliminarCentro').on('click', function () {
        $.ajax({
            url: '/admin/centros-medicos/' + centroIdEliminar,
            method: 'POST',
            data: { _token: csrf, _method: 'DELETE' },
            success: function () {
                mostrarToast('Centro eliminado', 'warning');
                modalEliminar.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                mostrarToast(xhr.responseJSON?.message ?? 'Error al eliminar', 'danger');
            }
        });
    });
});
</script>
@endsection