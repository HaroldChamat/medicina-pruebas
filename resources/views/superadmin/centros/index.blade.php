@extends('superadmin.superadmin')

@section('page-title', 'Centros Médicos')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:#64748b;">Centros Médicos</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <p class="text-muted mb-0 small">Gestión de centros médicos del sistema.</p>
    <button class="btn fw-semibold rounded-pill px-4"
            style="background:var(--sa-gold); color:#000; border:none;"
            id="btnNuevoCentro">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Centro
    </button>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm" style="border-radius:14px; overflow:hidden;">
    <div class="table-responsive">
        <table class="table sa-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th class="text-center">Admins</th>
                    <th class="text-center">Médicos</th>
                    <th class="text-center">Pacientes</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($centros as $centro)
                    <tr>
                        <td class="text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:8px;height:8px;border-radius:50%;background:var(--sa-gold);flex-shrink:0;"></div>
                                <span class="fw-semibold">{{ $centro->nombre }}</span>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $centro->direccion }}</td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(99,102,241,.15);color:#6366f1;">
                                {{ $centro->total_admins }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(16,185,129,.15);color:#10b981;">
                                {{ $centro->total_medicos }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(59,130,246,.15);color:#3b82f6;">
                                {{ $centro->total_pacientes }}
                            </span>
                        </td>
                        <td class="text-center fw-bold">{{ $centro->total_usuarios }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('superadmin.centros.show', $centro->id) }}"
                                   class="btn btn-sm rounded-pill"
                                   style="background:rgba(212,160,23,.1);color:var(--sa-gold);border:1px solid rgba(212,160,23,.2);">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm rounded-pill btnEditarCentro"
                                        style="background:rgba(99,102,241,.1);color:#6366f1;border:1px solid rgba(99,102,241,.2);"
                                        data-id="{{ $centro->id }}"
                                        data-nombre="{{ $centro->nombre }}"
                                        data-direccion="{{ $centro->direccion }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm rounded-pill btnEliminarCentro"
                                        style="background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.2);"
                                        data-id="{{ $centro->id }}"
                                        data-nombre="{{ $centro->nombre }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-building-slash fs-2 d-block mb-2"></i>
                            No hay centros médicos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ MODAL CREAR/EDITAR ═══ --}}
<div class="modal fade" id="modalCentro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--sa-border);background:#111827;">
            <div class="modal-header" style="background:var(--sa-navy);border-bottom:1px solid var(--sa-border);border-radius:16px 16px 0 0;">
                <h5 class="modal-title text-white fw-bold" id="tituloCentro">
                    <i class="bi bi-building-fill me-2" style="color:var(--sa-gold);"></i>Nuevo Centro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <input type="hidden" id="centro_id">
                <div class="mb-3">
                    <label class="form-label small fw-semibold" style="color:#8aa0bc;">Nombre del centro</label>
                    <input type="text" id="centro_nombre" class="form-control sa-input" placeholder="Ej: Clínica Central">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" style="color:#8aa0bc;">Dirección</label>
                    <input type="text" id="centro_direccion" class="form-control sa-input" placeholder="Ej: Av. Principal 1234">
                </div>
            </div>
            <div class="modal-footer" style="background:#111827;border-top:1px solid var(--sa-border);border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn fw-semibold rounded-pill px-4" id="btnGuardarCentro"
                        style="background:var(--sa-gold);color:#000;border:none;">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL ELIMINAR ═══ --}}
<div class="modal fade" id="modalEliminarCentro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:1px solid rgba(239,68,68,.3);background:#111827;">
            <div class="modal-header" style="background:rgba(239,68,68,.15);border-bottom:1px solid rgba(239,68,68,.2);border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color:#ef4444;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Centro Médico
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <p class="text-white mb-3">
                    ¿Eliminar el centro <strong style="color:var(--sa-gold);" id="nombreCentroEliminar"></strong>?
                </p>
                <div id="previewInfo" class="rounded-3 p-3 mb-3 d-none"
                     style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);">
                    <p class="small fw-bold mb-2" style="color:#fbbf24;">
                        <i class="bi bi-info-circle me-1"></i> Se eliminarán permanentemente:
                    </p>
                    <div class="row g-2 small" style="color:#e2c97e;" id="previewData"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" style="color:#ef4444;">
                        Escribe <strong>CONFIRMAR</strong> para continuar:
                    </label>
                    <input type="text" id="inputConfirmar" class="form-control sa-input"
                           placeholder="CONFIRMAR">
                </div>
            </div>
            <div class="modal-footer" style="background:#111827;border-top:1px solid rgba(239,68,68,.2);border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn rounded-pill fw-semibold px-4" id="btnConfirmarEliminarCentro"
                        style="background:rgba(239,68,68,.8);color:#fff;border:none;" disabled>
                    <i class="bi bi-trash3 me-1"></i> Eliminar todo
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.sa-input {
    background:rgba(255,255,255,.06)!important;
    border:1px solid rgba(255,255,255,.12)!important;
    color:#fff!important;
    border-radius:10px!important;
}
.sa-input:focus {
    border-color:rgba(212,160,23,.5)!important;
    box-shadow:0 0 0 3px rgba(212,160,23,.12)!important;
    background:rgba(255,255,255,.09)!important;
}
.sa-input::placeholder{color:rgba(255,255,255,.25)!important;}
</style>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');
    let centroIdEliminar = null;
    let modoEditar = false;

    const modalCentro   = new bootstrap.Modal(document.getElementById('modalCentro'));
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminarCentro'));

    // ── CREAR ─────────────────────────────────────────────────────────
    $('#btnNuevoCentro').on('click', function () {
        modoEditar = false;
        $('#tituloCentro').html('<i class="bi bi-building-fill me-2" style="color:var(--sa-gold);"></i>Nuevo Centro');
        $('#centro_id').val('');
        $('#centro_nombre, #centro_direccion').val('');
        modalCentro.show();
    });

    // ── EDITAR ────────────────────────────────────────────────────────
    $(document).on('click', '.btnEditarCentro', function () {
        modoEditar = true;
        $('#tituloCentro').html('<i class="bi bi-pencil me-2" style="color:var(--sa-gold);"></i>Editar Centro');
        $('#centro_id').val($(this).data('id'));
        $('#centro_nombre').val($(this).data('nombre'));
        $('#centro_direccion').val($(this).data('direccion'));
        modalCentro.show();
    });

    // ── GUARDAR ───────────────────────────────────────────────────────
    $('#btnGuardarCentro').on('click', function () {
        const nombre    = $('#centro_nombre').val().trim();
        const direccion = $('#centro_direccion').val().trim();
        if (!nombre || !direccion) { saToast('Completa todos los campos', 'warning'); return; }

        const id     = $('#centro_id').val();
        const url    = modoEditar ? `/superadmin/centros/${id}` : '/superadmin/centros';
        const method = modoEditar ? 'PUT' : 'POST';
        const $btn   = $(this);

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url, method: 'POST',
            data: { _token: csrf, _method: method, nombre, direccion },
            success: function () {
                saToast(modoEditar ? 'Centro actualizado' : 'Centro creado correctamente', 'success');
                modalCentro.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                saToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar');
            }
        });
    });

    // ── ELIMINAR: preview ─────────────────────────────────────────────
    $(document).on('click', '.btnEliminarCentro', function () {
        centroIdEliminar = $(this).data('id');
        $('#nombreCentroEliminar').text($(this).data('nombre'));
        $('#inputConfirmar').val('');
        $('#btnConfirmarEliminarCentro').prop('disabled', true);
        $('#previewInfo').addClass('d-none');
        $('#previewData').empty();
        modalEliminar.show();

        // Cargar preview
        $.get(`/superadmin/centros/${centroIdEliminar}/preview-destroy`, function (data) {
            $('#previewData').html(`
                <div class="col-4"><i class="bi bi-shield-check me-1"></i>${data.admins} admin(s)</div>
                <div class="col-4"><i class="bi bi-person-badge me-1"></i>${data.medicos} médico(s)</div>
                <div class="col-4"><i class="bi bi-person-heart me-1"></i>${data.pacientes} paciente(s)</div>
                <div class="col-4"><i class="bi bi-calendar me-1"></i>${data.citas} cita(s)</div>
                <div class="col-4"><i class="bi bi-ticket me-1"></i>${data.tickets} ticket(s)</div>
                <div class="col-4"><i class="bi bi-file-earmark me-1"></i>${data.informes} informe(s)</div>
            `);
            $('#previewInfo').removeClass('d-none');
        });
    });

    $('#inputConfirmar').on('input', function () {
        $('#btnConfirmarEliminarCentro').prop('disabled', $(this).val() !== 'CONFIRMAR');
    });

    // ── ELIMINAR: confirmar ───────────────────────────────────────────
    $('#btnConfirmarEliminarCentro').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>...');

        $.ajax({
            url: `/superadmin/centros/${centroIdEliminar}`,
            method: 'POST',
            data: { _token: csrf, _method: 'DELETE', confirm: 'CONFIRMAR' },
            success: function () {
                saToast('Centro eliminado correctamente', 'success');
                modalEliminar.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                saToast(xhr.responseJSON?.error ?? 'Error al eliminar', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-trash3 me-1"></i> Eliminar todo');
            }
        });
    });
});
</script>
@endsection

