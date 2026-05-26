@extends('superadmin.superadmin')

@section('page-title', 'Centros Médicos')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:#64748b;">Centros Médicos</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <p class="text-muted mb-0 small">Gestión global de todos los centros médicos del sistema.</p>
    </div>
    <button class="btn fw-semibold rounded-pill px-4"
            style="background:var(--sa-gold); color:#000; border:none;"
            id="btnNuevoCentro">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Centro
    </button>
</div>

{{-- Filtro --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
    <div class="card-body py-3 px-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" id="buscadorCentros" class="form-control form-control-sm"
                       placeholder="🔍 Buscar por nombre o dirección...">
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm" style="border-radius:14px; overflow:hidden;">
    <div class="table-responsive">
        <table class="table sa-table mb-0" id="tablaCentros">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th class="text-center">Admins</th>
                    <th class="text-center">Médicos</th>
                    <th class="text-center">Pacientes</th>
                    <th class="text-center">Creado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($centros as $centro)
                    <tr data-busqueda="{{ strtolower($centro->nombre . ' ' . $centro->direccion) }}">
                        <td class="text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:10px; height:10px; border-radius:50%; background:var(--sa-gold); flex-shrink:0;"></div>
                                <span class="fw-semibold">{{ $centro->nombre }}</span>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $centro->direccion }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill" style="background:rgba(99,102,241,.15); color:#6366f1;">
                                {{ $centro->total_admins }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill" style="background:rgba(16,185,129,.15); color:#10b981;">
                                {{ $centro->total_medicos }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill" style="background:rgba(59,130,246,.15); color:#3b82f6;">
                                {{ $centro->total_pacientes }}
                            </span>
                        </td>
                        <td class="text-center text-muted small">
                            {{ $centro->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('superadmin.centros.show', $centro->id) }}"
                                   class="btn btn-sm rounded-pill"
                                   title="Ver detalle"
                                   style="background:rgba(212,160,23,.1); color:var(--sa-gold); border:1px solid rgba(212,160,23,.2);">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm rounded-pill btnEditarCentro"
                                        title="Editar"
                                        style="background:rgba(99,102,241,.1); color:#6366f1; border:1px solid rgba(99,102,241,.2);"
                                        data-id="{{ $centro->id }}"
                                        data-nombre="{{ $centro->nombre }}"
                                        data-direccion="{{ $centro->direccion }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm rounded-pill btnEliminarCentro"
                                        title="Eliminar"
                                        style="background:rgba(239,68,68,.1); color:#ef4444; border:1px solid rgba(239,68,68,.2);"
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
        <div class="modal-content" style="border-radius:16px; border:1px solid var(--sa-border); background:#111827;">
            <div class="modal-header" style="background:var(--sa-navy); border-bottom:1px solid var(--sa-border); border-radius:16px 16px 0 0;">
                <h5 class="modal-title text-white fw-bold" id="tituloCentro">
                    <i class="bi bi-building me-2" style="color:var(--sa-gold);"></i>Nuevo Centro
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <input type="hidden" id="centro_id">
                <div class="mb-3">
                    <label class="form-label small fw-semibold" style="color:#8aa0bc;">Nombre del centro</label>
                    <input type="text" id="centro_nombre" class="form-control"
                           placeholder="Ej: Clínica Central Norte"
                           style="background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#fff; border-radius:10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" style="color:#8aa0bc;">Dirección</label>
                    <input type="text" id="centro_direccion" class="form-control"
                           placeholder="Ej: Av. Principal 1234, Santiago"
                           style="background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#fff; border-radius:10px;">
                </div>
            </div>
            <div class="modal-footer" style="background:#111827; border-top:1px solid var(--sa-border); border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn fw-semibold rounded-pill px-4" id="btnGuardarCentro"
                        style="background:var(--sa-gold); color:#000; border:none;">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL ELIMINAR PASO 1 ═══ --}}
<div class="modal fade" id="modalEliminarP1" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:1px solid rgba(239,68,68,.3); background:#111827;">
            <div class="modal-header" style="background:rgba(239,68,68,.15); border-bottom:1px solid rgba(239,68,68,.2); border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color:#ef4444;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Primera confirmación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <p class="text-white mb-3">
                    Estás a punto de eliminar el centro
                    <strong style="color:var(--sa-gold);" id="p1NombreCentro"></strong>.
                </p>

                <div class="rounded-3 p-3 mb-3" style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.2);">
                    <p class="small fw-bold mb-2" style="color:#ef4444;">
                        <i class="bi bi-exclamation-circle me-1"></i>Se eliminará permanentemente:
                    </p>
                    <ul class="small mb-0" style="color:#fca5a5;" id="p1ListaAfectados">
                        <li>Cargando...</li>
                    </ul>
                </div>

                <div class="rounded-3 p-3" style="background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.2);">
                    <p class="small mb-0" style="color:#fbbf24;">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        <strong>Esta acción es irreversible.</strong>
                        No podrás recuperar ninguno de estos datos.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="background:#111827; border-top:1px solid rgba(239,68,68,.2); border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar, mantener</button>
                <button class="btn rounded-pill fw-semibold px-4" id="btnP1Continuar"
                        style="background:rgba(239,68,68,.8); color:#fff; border:none;">
                    <i class="bi bi-arrow-right me-1"></i> Sí, continuar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL ELIMINAR PASO 2 ═══ --}}
<div class="modal fade" id="modalEliminarP2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:2px solid #ef4444; background:#111827;">
            <div class="modal-header" style="background:#ef4444; border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold text-white">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i>CONFIRMACIÓN FINAL
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <p class="text-white mb-4">
                    ⚠️ Esta es tu <strong>última oportunidad</strong> para cancelar.
                    Escribe <code style="color:var(--sa-gold); background:rgba(212,160,23,.1); padding:2px 6px; border-radius:4px;">CONFIRMAR</code>
                    para eliminar permanentemente
                    <strong style="color:var(--sa-gold);" id="p2NombreCentro"></strong>
                    y todos sus datos.
                </p>

                <input type="text" id="inputConfirmar" class="form-control text-center fw-bold"
                       placeholder="Escribe CONFIRMAR"
                       style="background:rgba(239,68,68,.08); border:2px solid rgba(239,68,68,.4); color:#fff; border-radius:10px; font-size:1.1rem; letter-spacing:2px;">
                <div id="errorConfirmar" class="text-danger small mt-2 d-none">
                    <i class="bi bi-x-circle me-1"></i>Debes escribir exactamente "CONFIRMAR"
                </div>
            </div>
            <div class="modal-footer" style="background:#111827; border-top:2px solid rgba(239,68,68,.3); border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn rounded-pill fw-bold px-4" id="btnEliminarFinal"
                        style="background:#ef4444; color:#fff; border:none;">
                    <i class="bi bi-trash3-fill me-1"></i>ELIMINAR DEFINITIVAMENTE
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');
    let centroIdAccion = null;
    let modoEditar = false;

    const modalCentro  = new bootstrap.Modal(document.getElementById('modalCentro'));
    const modalP1      = new bootstrap.Modal(document.getElementById('modalEliminarP1'));
    const modalP2      = new bootstrap.Modal(document.getElementById('modalEliminarP2'));

    // ── BUSCADOR ─────────────────────────────────────────────────────────
    $('#buscadorCentros').on('keyup', function () {
        const txt = $(this).val().toLowerCase();
        $('#tablaCentros tbody tr').each(function () {
            $(this).toggle(!txt || $(this).data('busqueda').includes(txt));
        });
    });

    // ── CREAR ────────────────────────────────────────────────────────────
    $('#btnNuevoCentro').on('click', function () {
        modoEditar = false;
        centroIdAccion = null;
        $('#tituloCentro').html('<i class="bi bi-building me-2" style="color:var(--sa-gold);"></i>Nuevo Centro');
        $('#centro_nombre, #centro_direccion').val('');
        modalCentro.show();
    });

    // ── EDITAR ───────────────────────────────────────────────────────────
    $(document).on('click', '.btnEditarCentro', function () {
        modoEditar = true;
        centroIdAccion = $(this).data('id');
        $('#tituloCentro').html('<i class="bi bi-pencil me-2" style="color:var(--sa-gold);"></i>Editar Centro');
        $('#centro_nombre').val($(this).data('nombre'));
        $('#centro_direccion').val($(this).data('direccion'));
        modalCentro.show();
    });

    $('#btnGuardarCentro').on('click', function () {
        const nombre    = $('#centro_nombre').val().trim();
        const direccion = $('#centro_direccion').val().trim();
        if (!nombre || !direccion) { saToast('Completa todos los campos', 'warning'); return; }

        const url    = modoEditar ? `/superadmin/centros/${centroIdAccion}` : '/superadmin/centros';
        const method = modoEditar ? 'PUT' : 'POST';

        $.ajax({
            url, method: 'POST',
            data: { _token: csrf, _method: method, nombre, direccion },
            success: function () {
                saToast('Centro guardado correctamente', 'success');
                modalCentro.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                saToast(xhr.responseJSON?.message ?? 'Error al guardar', 'danger');
            }
        });
    });

    // ── ELIMINAR PASO 1 ──────────────────────────────────────────────────
    $(document).on('click', '.btnEliminarCentro', function () {
        centroIdAccion = $(this).data('id');
        const nombre   = $(this).data('nombre');
        $('#p1NombreCentro').text(nombre);
        $('#p1ListaAfectados').html('<li>Cargando información...</li>');
        modalP1.show();

        // Cargar preview de lo que se eliminará
        $.get(`/superadmin/centros/${centroIdAccion}/preview-destroy`, function (data) {
            const html = `
                <li>${data.admins} administrador(es)</li>
                <li>${data.medicos} médico(s)</li>
                <li>${data.pacientes} paciente(s)</li>
                <li>${data.citas} cita(s) médica(s)</li>
                <li>${data.informes} informe(s) médico(s)</li>
                <li>${data.tickets} ticket(s) de soporte</li>
                <li>Todos los mensajes, notificaciones y archivos asociados</li>
            `;
            $('#p1ListaAfectados').html(html);
        }).fail(function () {
            $('#p1ListaAfectados').html('<li>Error al cargar la información</li>');
        });
    });

    $('#btnP1Continuar').on('click', function () {
        modalP1.hide();
        $('#inputConfirmar').val('');
        $('#errorConfirmar').addClass('d-none');
        $('#p2NombreCentro').text($('#p1NombreCentro').text());
        setTimeout(() => modalP2.show(), 400);
    });

    // ── ELIMINAR PASO 2 ──────────────────────────────────────────────────
    $('#btnEliminarFinal').on('click', function () {
        const confirmText = $('#inputConfirmar').val().trim();
        if (confirmText !== 'CONFIRMAR') {
            $('#errorConfirmar').removeClass('d-none');
            $('#inputConfirmar').css('border-color', '#ef4444');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...');

        $.ajax({
            url: `/superadmin/centros/${centroIdAccion}`,
            method: 'POST',
            data: { _token: csrf, _method: 'DELETE', confirm: 'CONFIRMAR' },
            success: function () {
                saToast('Centro eliminado permanentemente', 'success');
                modalP2.hide();
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                saToast(xhr.responseJSON?.error ?? 'Error al eliminar', 'danger');
                $btn.prop('disabled', false).html('<i class="bi bi-trash3-fill me-1"></i>ELIMINAR DEFINITIVAMENTE');
            }
        });
    });

    $('#inputConfirmar').on('input', function () {
        if ($(this).val() === 'CONFIRMAR') {
            $('#errorConfirmar').addClass('d-none');
            $(this).css('border-color', '#10b981');
        } else {
            $(this).css('border-color', 'rgba(239,68,68,.4)');
        }
    });
});
</script>
@endsection