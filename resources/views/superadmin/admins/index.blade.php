@extends('superadmin.superadmin')

@section('page-title', 'Administradores')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:#64748b;">Administradores</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <p class="text-muted mb-0 small">Gestión de administradores de centros médicos.</p>
    <button class="btn fw-semibold rounded-pill px-4"
            style="background:var(--sa-gold);color:#000;border:none;"
            id="btnNuevoAdmin">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Administrador
    </button>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
    <div class="card-body py-3 px-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" id="buscadorAdmins" class="form-control form-control-sm"
                       placeholder="🔍 Buscar por nombre, email o RUT...">
            </div>
            <div class="col-md-4">
                <select id="filtroCentro" class="form-select form-select-sm">
                    <option value="">Todos los centros</option>
                    @foreach($centros as $centro)
                        <option value="{{ $centro->id }}" {{ $filtroCentro == $centro->id ? 'selected' : '' }}>
                            {{ $centro->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-secondary w-100 rounded-pill" id="btnLimpiarFiltros">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <div class="table-responsive">
        <table class="table sa-table mb-0" id="tablaAdmins">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>RUT</th>
                    <th>Teléfono</th>
                    <th>Centro Médico</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                    <tr data-busqueda="{{ strtolower($admin->name.' '.$admin->Apellidos.' '.$admin->email.' '.$admin->Rut) }}">
                        <td class="text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;
                                     background:linear-gradient(135deg,#0d3b6e,#1a6fa8);
                                     display:flex;align-items:center;justify-content:center;
                                     color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                    {{ strtoupper(substr($admin->name,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $admin->name }} {{ $admin->Apellidos }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $admin->email }}</td>
                        <td>
                            <span class="badge rounded-pill"
                                  style="background:rgba(212,160,23,.1);color:var(--sa-gold);font-size:.72rem;">
                                {{ $admin->Rut }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $admin->telefono ?? '—' }}</td>
                        <td>
                            @if($admin->centroMedico)
                                <span class="badge rounded-pill"
                                      style="background:rgba(16,185,129,.12);color:#10b981;">
                                    <i class="bi bi-building me-1"></i>{{ $admin->centroMedico->nombre }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Sin centro</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm rounded-pill btnEditarAdmin"
                                        title="Editar"
                                        style="background:rgba(99,102,241,.1);color:#6366f1;border:1px solid rgba(99,102,241,.2);"
                                        data-id="{{ $admin->id }}"
                                        data-name="{{ $admin->name }}"
                                        data-apellidos="{{ $admin->Apellidos }}"
                                        data-email="{{ $admin->email }}"
                                        data-rut="{{ $admin->Rut }}"
                                        data-telefono="{{ $admin->telefono }}"
                                        data-centro="{{ $admin->centro_medico_id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm rounded-pill btnEliminarAdmin"
                                        title="Eliminar"
                                        style="background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.2);"
                                        data-id="{{ $admin->id }}"
                                        data-name="{{ $admin->name }} {{ $admin->Apellidos }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-shield-slash fs-2 d-block mb-2"></i>
                            No hay administradores registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL CREAR/EDITAR --}}
<div class="modal fade" id="modalAdmin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--sa-border);background:#111827;">
            <div class="modal-header" style="background:var(--sa-navy);border-bottom:1px solid var(--sa-border);border-radius:16px 16px 0 0;">
                <h5 class="modal-title text-white fw-bold" id="tituloAdmin">
                    <i class="bi bi-shield-plus me-2" style="color:var(--sa-gold);"></i>Nuevo Administrador
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <input type="hidden" id="admin_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">Nombre</label>
                        <input type="text" id="admin_name" class="form-control sa-input" placeholder="Nombre">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">Apellidos</label>
                        <input type="text" id="admin_apellidos" class="form-control sa-input" placeholder="Apellidos">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">Email</label>
                        <input type="email" id="admin_email" class="form-control sa-input" placeholder="admin@clinica.cl">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">RUT</label>
                        <input type="text" id="admin_rut" class="form-control sa-input" placeholder="12345678-9" maxlength="12">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">Teléfono</label>
                        <input type="text" id="admin_telefono" class="form-control sa-input" placeholder="+56912345678">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">Centro Médico</label>
                        <select id="admin_centro" class="form-select sa-input">
                            <option value="">Seleccione un centro</option>
                            @foreach($centros as $centro)
                                <option value="{{ $centro->id }}">{{ $centro->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">
                            Contraseña <span id="passHint" class="opacity-50 fw-normal">(dejar vacío para no cambiar)</span>
                        </label>
                        <div class="input-group">
                            <input type="password" id="admin_password" class="form-control sa-input"
                                   placeholder="Mínimo 6 caracteres">
                            <button class="btn" type="button" id="toggleAdminPass"
                                    style="border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.4);background:rgba(255,255,255,.04);">
                                <i class="bi bi-eye-fill" id="adminEye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#111827;border-top:1px solid var(--sa-border);border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn fw-semibold rounded-pill px-4" id="btnGuardarAdmin"
                        style="background:var(--sa-gold);color:#000;border:none;">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ELIMINAR --}}
<div class="modal fade" id="modalEliminarAdmin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid rgba(239,68,68,.3);background:#111827;">
            <div class="modal-header" style="background:rgba(239,68,68,.15);border-bottom:1px solid rgba(239,68,68,.2);border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color:#ef4444;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar Administrador
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <p class="text-white">
                    ¿Eliminar al administrador <strong style="color:var(--sa-gold);" id="nombreAdminEliminar"></strong>?
                </p>
                <div class="rounded-3 p-3" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);">
                    <p class="small mb-0" style="color:#fbbf24;">
                        <i class="bi bi-info-circle me-1"></i>
                        El administrador perderá acceso al sistema. Los datos de su centro permanecerán.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="background:#111827;border-top:1px solid rgba(239,68,68,.2);border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn rounded-pill fw-semibold px-4" id="btnConfirmarEliminarAdmin"
                        style="background:rgba(239,68,68,.8);color:#fff;border:none;">
                    <i class="bi bi-trash3 me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.sa-input{background:rgba(255,255,255,.06)!important;border:1px solid rgba(255,255,255,.12)!important;color:#fff!important;border-radius:10px!important;}
.sa-input:focus{border-color:rgba(212,160,23,.5)!important;box-shadow:0 0 0 3px rgba(212,160,23,.12)!important;background:rgba(255,255,255,.09)!important;}
.sa-input::placeholder{color:rgba(255,255,255,.25)!important;}
.sa-input option{background:#1e293b;color:#fff;}
</style>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');
    let adminIdAccion = null;
    let modoEditar = false;

    const modalAdmin = new bootstrap.Modal(document.getElementById('modalAdmin'));
    const modalElim  = new bootstrap.Modal(document.getElementById('modalEliminarAdmin'));

    $('#toggleAdminPass').on('click', function () {
        const t = $('#admin_password').attr('type') === 'password' ? 'text' : 'password';
        $('#admin_password').attr('type', t);
        $('#adminEye').toggleClass('bi-eye-fill bi-eye-slash-fill');
    });

    function formatRut(rut) {
        const limpio = rut.replace(/[^0-9kK]/g, '');
        if (limpio.length < 2) return limpio;
        return limpio.slice(0, -1) + '-' + limpio.slice(-1).toUpperCase();
    }
    $('#admin_rut').on('input', function () { this.value = formatRut(this.value); });

    $('#buscadorAdmins').on('keyup', function () {
        const txt = $(this).val().toLowerCase();
        $('#tablaAdmins tbody tr').each(function () {
            $(this).toggle(!txt || $(this).data('busqueda').includes(txt));
        });
    });

    $('#filtroCentro').on('change', function () {
        const centroId = $(this).val();
        window.location.href = centroId
            ? `{{ route('superadmin.admins.index') }}?centro_id=${centroId}`
            : `{{ route('superadmin.admins.index') }}`;
    });

    $('#btnLimpiarFiltros').on('click', function () {
        window.location.href = `{{ route('superadmin.admins.index') }}`;
    });

    $('#btnNuevoAdmin').on('click', function () {
        modoEditar = false; adminIdAccion = null;
        $('#tituloAdmin').html('<i class="bi bi-shield-plus me-2" style="color:var(--sa-gold);"></i>Nuevo Administrador');
        $('#admin_id, #admin_name, #admin_apellidos, #admin_email, #admin_rut, #admin_telefono, #admin_password').val('');
        $('#admin_centro').val('');
        $('#passHint').text('(requerida)');
        modalAdmin.show();
    });

    $(document).on('click', '.btnEditarAdmin', function () {
        modoEditar = true; adminIdAccion = $(this).data('id');
        $('#tituloAdmin').html('<i class="bi bi-pencil me-2" style="color:var(--sa-gold);"></i>Editar Administrador');
        $('#admin_id').val(adminIdAccion);
        $('#admin_name').val($(this).data('name'));
        $('#admin_apellidos').val($(this).data('apellidos'));
        $('#admin_email').val($(this).data('email'));
        $('#admin_rut').val($(this).data('rut'));
        $('#admin_telefono').val($(this).data('telefono'));
        $('#admin_centro').val($(this).data('centro'));
        $('#admin_password').val('');
        $('#passHint').text('(dejar vacío para no cambiar)');
        modalAdmin.show();
    });

    $('#btnGuardarAdmin').on('click', function () {
        const name      = $('#admin_name').val().trim();
        const apellidos = $('#admin_apellidos').val().trim();
        const email     = $('#admin_email').val().trim();
        const rut       = $('#admin_rut').val().trim();
        const telefono  = $('#admin_telefono').val().trim();
        const centro    = $('#admin_centro').val();
        const password  = $('#admin_password').val();

        if (!name || !apellidos || !email || !centro) {
            saToast('Completa todos los campos obligatorios', 'warning'); return;
        }
        if (!modoEditar && !password) {
            saToast('La contraseña es obligatoria para nuevos admins', 'warning'); return;
        }

        const datos = { _token: csrf, name, Apellidos: apellidos, email, Rut: rut, telefono, centro_medico_id: centro };
        if (!modoEditar) datos.password = password;
        else if (password) datos.password = password;

        const url    = modoEditar ? `/superadmin/admins/${adminIdAccion}` : '/superadmin/admins';
        const method = modoEditar ? 'PUT' : 'POST';
        const $btn   = $(this);

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.ajax({
            url, method: 'POST',
            data: { ...datos, _method: method },
            success: function () {
                saToast(modoEditar ? 'Administrador actualizado' : 'Administrador creado correctamente', 'success');
                modalAdmin.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                const msg = Object.values(xhr.responseJSON?.errors ?? {}).flat()[0]
                    ?? xhr.responseJSON?.message ?? 'Error al guardar';
                saToast(msg, 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar');
            }
        });
    });

    $(document).on('click', '.btnEliminarAdmin', function () {
        adminIdAccion = $(this).data('id');
        $('#nombreAdminEliminar').text($(this).data('name'));
        modalElim.show();
    });

    $('#btnConfirmarEliminarAdmin').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>...');
        $.ajax({
            url: `/superadmin/admins/${adminIdAccion}`,
            method: 'POST',
            data: { _token: csrf, _method: 'DELETE' },
            success: function () {
                saToast('Administrador eliminado', 'success');
                modalElim.hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                saToast(xhr.responseJSON?.message ?? 'Error al eliminar', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bi bi-trash3 me-1"></i> Sí, eliminar');
            }
        });
    });
});
</script>
@endsection