@extends('superadmin.superadmin')

@section('page-title', 'Pacientes')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:#64748b;">Pacientes</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <p class="text-muted mb-0 small">
        Todos los pacientes registrados en el sistema.
        <strong>{{ $pacientes->total() }}</strong> en total.
    </p>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
    <div class="card-body py-3 px-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" id="buscador" class="form-control form-control-sm"
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
                <button class="btn btn-sm btn-outline-secondary w-100 rounded-pill" id="btnLimpiar">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <div class="table-responsive">
        <table class="table sa-table mb-0" id="tablaPacientes">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>RUT</th>
                    <th>Teléfono</th>
                    <th>Centro</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pacientes as $paciente)
                    <tr data-busqueda="{{ strtolower($paciente->name.' '.$paciente->Apellidos.' '.$paciente->email.' '.$paciente->Rut) }}">
                        <td class="text-muted">{{ ($pacientes->currentPage() - 1) * $pacientes->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;
                                     background:linear-gradient(135deg,#1565c0,#42a5f5);
                                     display:flex;align-items:center;justify-content:center;
                                     color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                    {{ strtoupper(substr($paciente->name,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $paciente->name }} {{ $paciente->Apellidos }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $paciente->email }}</td>
                        <td>
                            <span class="badge rounded-pill"
                                  style="background:rgba(59,130,246,.1);color:#3b82f6;font-size:.72rem;">
                                {{ $paciente->Rut }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $paciente->telefono ?? '—' }}</td>
                        <td>
                            @if($paciente->centroMedico)
                                <span class="badge rounded-pill"
                                      style="background:rgba(16,185,129,.12);color:#10b981;font-size:.72rem;">
                                    <i class="bi bi-building me-1"></i>{{ $paciente->centroMedico->nombre }}
                                </span>
                            @else
                                <span class="text-muted small fst-italic">Sin centro</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Ver / Editar --}}
                                <button class="btn btn-sm rounded-pill btn-ver-paciente"
                                        title="Ver y editar"
                                        style="background:rgba(212,160,23,.1);color:var(--sa-gold);border:1px solid rgba(212,160,23,.2);"
                                        data-id="{{ $paciente->id }}"
                                        data-name="{{ $paciente->name }}"
                                        data-apellidos="{{ $paciente->Apellidos ?? '' }}"
                                        data-email="{{ $paciente->email }}"
                                        data-rut="{{ $paciente->Rut }}"
                                        data-telefono="{{ $paciente->telefono ?? '' }}"
                                        data-activo="{{ $paciente->activo }}"
                                        data-centro="{{ $paciente->centro_medico_id ?? '' }}"
                                        data-centro-nombre="{{ $paciente->centroMedico->nombre ?? 'Sin centro' }}">
                                    <i class="bi bi-eye"></i>
                                </button>

                                {{-- Cambiar centro --}}
                                <button class="btn btn-sm rounded-pill btn-cambiar-centro"
                                        title="Cambiar centro médico"
                                        style="background:rgba(99,102,241,.1);color:#6366f1;border:1px solid rgba(99,102,241,.2);"
                                        data-id="{{ $paciente->id }}"
                                        data-name="{{ $paciente->name }} {{ $paciente->Apellidos }}"
                                        data-centro="{{ $paciente->centro_medico_id ?? '' }}"
                                        data-centro-nombre="{{ $paciente->centroMedico->nombre ?? 'Sin centro' }}">
                                    <i class="bi bi-building-gear"></i>
                                </button>

                                {{-- Eliminar --}}
                                <button class="btn btn-sm rounded-pill btn-eliminar-paciente"
                                        title="Eliminar"
                                        style="background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.2);"
                                        data-id="{{ $paciente->id }}"
                                        data-name="{{ $paciente->name }} {{ $paciente->Apellidos }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-person-heart fs-2 d-block mb-2"></i>
                            No hay pacientes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pacientes->hasPages())
        <div class="d-flex justify-content-between align-items-center px-4 py-3"
             style="border-top:1px solid #e2e8f0;">
            <div class="text-muted small">
                Mostrando {{ $pacientes->firstItem() }}–{{ $pacientes->lastItem() }}
                de {{ $pacientes->total() }} pacientes
            </div>
            @include('superadmin.partials.pagination', ['paginator' => $pacientes])
        </div>
    @endif
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL — VER / EDITAR PACIENTE
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalVerPaciente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:1px solid var(--sa-border);background:#111827;">

            {{-- Header --}}
            <div class="modal-header" style="background:var(--sa-navy);border-bottom:1px solid var(--sa-border);border-radius:16px 16px 0 0;">
                <div class="d-flex align-items-center gap-3">
                    <div id="modal-avatar"
                         style="width:40px;height:40px;border-radius:50%;
                                background:linear-gradient(135deg,#1565c0,#42a5f5);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-weight:700;font-size:1rem;flex-shrink:0;">
                        P
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0" id="modal-titulo">Paciente</h5>
                        <small id="modal-subtitulo" style="color:rgba(255,255,255,.5);font-size:.75rem;"></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Tabs --}}
            <div style="background:var(--sa-navy);border-bottom:1px solid var(--sa-border);padding:0 1.5rem;">
                <ul class="nav" id="pacienteTabs" style="gap:.25rem;">
                    <li class="nav-item">
                        <button class="nav-link active sa-modal-tab" data-tab="info" style="color:rgba(255,255,255,.5);background:none;border:none;padding:.75rem 1rem;font-size:.85rem;font-weight:600;border-bottom:2px solid transparent;">
                            <i class="bi bi-person me-1"></i> Datos personales
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link sa-modal-tab" data-tab="acceso" style="color:rgba(255,255,255,.5);background:none;border:none;padding:.75rem 1rem;font-size:.85rem;font-weight:600;border-bottom:2px solid transparent;">
                            <i class="bi bi-shield-lock me-1"></i> Acceso
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link sa-modal-tab" data-tab="centro" style="color:rgba(255,255,255,.5);background:none;border:none;padding:.75rem 1rem;font-size:.85rem;font-weight:600;border-bottom:2px solid transparent;">
                            <i class="bi bi-building me-1"></i> Centro médico
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4" style="background:#111827;">
                <input type="hidden" id="pac_id">

                {{-- TAB: Datos personales --}}
                <div id="tab-info" class="sa-tab-panel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">Nombre</label>
                            <input type="text" id="pac_name" class="form-control sa-modal-input" placeholder="Nombre">
                            <div class="sa-field-error" id="err_name"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">Apellidos</label>
                            <input type="text" id="pac_apellidos" class="form-control sa-modal-input" placeholder="Apellidos">
                            <div class="sa-field-error" id="err_apellidos"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">Email</label>
                            <input type="email" id="pac_email" class="form-control sa-modal-input" placeholder="email@ejemplo.com">
                            <div class="sa-field-error" id="err_email"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">RUT</label>
                            <input type="text" id="pac_rut" class="form-control sa-modal-input" placeholder="12345678-9" maxlength="12">
                            <div class="sa-field-error" id="err_rut"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">Teléfono</label>
                            <input type="text" id="pac_telefono" class="form-control sa-modal-input" placeholder="+56912345678" maxlength="20">
                            <div class="sa-field-error" id="err_telefono"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">Estado</label>
                            <select id="pac_activo" class="form-select sa-modal-input">
                                <option value="1">✅ Activo</option>
                                <option value="0">⛔ Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- TAB: Acceso / contraseña --}}
                <div id="tab-acceso" class="sa-tab-panel d-none">
                    <div class="rounded-3 p-3 mb-4" style="background:rgba(212,160,23,.08);border:1px solid rgba(212,160,23,.2);">
                        <p class="small mb-0" style="color:#fbbf24;">
                            <i class="bi bi-info-circle me-1"></i>
                            Deja los campos en blanco para mantener la contraseña actual sin cambios.
                        </p>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" id="pac_password" class="form-control sa-modal-input"
                                       placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                                <button class="btn" type="button" id="togglePacPass"
                                        style="border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.4);background:rgba(255,255,255,.04);">
                                    <i class="bi bi-eye-fill" id="eyePacPass"></i>
                                </button>
                            </div>
                            <div class="sa-field-error" id="err_password"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color:#8aa0bc;">Confirmar contraseña</label>
                            <div class="input-group">
                                <input type="password" id="pac_password_confirm" class="form-control sa-modal-input"
                                       placeholder="Repite la contraseña" autocomplete="new-password">
                                <button class="btn" type="button" id="togglePacPassConfirm"
                                        style="border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.4);background:rgba(255,255,255,.04);">
                                    <i class="bi bi-eye-fill" id="eyePacPassConfirm"></i>
                                </button>
                            </div>
                            <div class="sa-field-error" id="err_password_confirm"></div>
                        </div>
                    </div>
                </div>

                {{-- TAB: Centro médico --}}
                <div id="tab-centro" class="sa-tab-panel d-none">
                    <div class="rounded-3 p-3 mb-4 d-flex align-items-center gap-3"
                         style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);">
                        <i class="bi bi-building-fill fs-4" style="color:#10b981;flex-shrink:0;"></i>
                        <div>
                            <p class="mb-0 small fw-semibold" style="color:#10b981;">Centro actual</p>
                            <p class="mb-0 fw-bold text-white" id="centro-actual-label">—</p>
                        </div>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold" style="color:#8aa0bc;">
                            Nuevo centro médico <span style="color:#ef4444;">*</span>
                        </label>
                        <select id="pac_centro" class="form-select sa-modal-input">
                            <option value="">— Selecciona el nuevo centro —</option>
                            @foreach($centros as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="sa-field-error" id="err_centro"></div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn fw-semibold rounded-pill px-4" id="btnConfirmarCentro"
                                style="background:rgba(99,102,241,.8);color:#fff;border:none;">
                            <span class="sa-btn-spinner" id="spin-centro"></span>
                            <span id="txt-centro">Confirmar cambio de centro</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer" style="background:#111827;border-top:1px solid var(--sa-border);border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn fw-semibold rounded-pill px-4" id="btnGuardarPaciente"
                        style="background:var(--sa-gold);color:#000;border:none;">
                    <span class="sa-btn-spinner" id="spin-guardar"></span>
                    <span id="txt-guardar">Guardar cambios</span>
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL — CAMBIAR CENTRO (acceso rápido desde botón de tabla)
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCambioCentroRapido" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid rgba(99,102,241,.3);background:#111827;">
            <div class="modal-header" style="background:rgba(99,102,241,.15);border-bottom:1px solid rgba(99,102,241,.2);border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color:#a5b4fc;">
                    <i class="bi bi-building-gear me-2"></i>Cambiar centro médico
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <input type="hidden" id="rapido_pac_id">

                {{-- Info paciente --}}
                <div class="d-flex align-items-center gap-3 rounded-3 p-3 mb-4"
                     style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);">
                    <div id="rapido-avatar"
                         style="width:38px;height:38px;border-radius:50%;
                                background:linear-gradient(135deg,#1565c0,#42a5f5);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0;">
                        P
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold text-white" id="rapido-nombre">—</p>
                        <p class="mb-0 small" style="color:#6366f1;">
                            Centro actual: <strong id="rapido-centro-actual">—</strong>
                        </p>
                    </div>
                </div>

                <label class="form-label small fw-semibold" style="color:#8aa0bc;">
                    Nuevo centro médico <span style="color:#ef4444;">*</span>
                </label>
                <select id="rapido_centro" class="form-select sa-modal-input">
                    <option value="">— Selecciona el nuevo centro —</option>
                    @foreach($centros as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <div class="sa-field-error" id="err_rapido_centro"></div>
            </div>
            <div class="modal-footer" style="background:#111827;border-top:1px solid rgba(99,102,241,.2);border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn fw-semibold rounded-pill px-4" id="btnConfirmarCentroRapido"
                        style="background:rgba(99,102,241,.8);color:#fff;border:none;">
                    <span class="sa-btn-spinner" id="spin-rapido"></span>
                    <span id="txt-rapido">Confirmar cambio</span>
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL — CONFIRMAR ELIMINAR
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEliminarPaciente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:1px solid rgba(239,68,68,.3);background:#111827;">
            <div class="modal-header" style="background:rgba(239,68,68,.15);border-bottom:1px solid rgba(239,68,68,.2);border-radius:16px 16px 0 0;">
                <h5 class="modal-title fw-bold" style="color:#ef4444;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Eliminar paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4" style="background:#111827;">
                <input type="hidden" id="elim_pac_id">
                <p class="text-white mb-3">
                    ¿Eliminar al paciente <strong style="color:var(--sa-gold);" id="elim_pac_nombre">—</strong>?
                </p>
                <div class="rounded-3 p-3" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);">
                    <p class="small mb-0" style="color:#fbbf24;">
                        <i class="bi bi-info-circle me-1"></i>
                        Esta acción no se puede deshacer. Las citas con informes médicos
                        se conservarán en el sistema.
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="background:#111827;border-top:1px solid rgba(239,68,68,.2);border-radius:0 0 16px 16px;">
                <button class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn fw-semibold rounded-pill px-4" id="btnConfirmarEliminar"
                        style="background:rgba(239,68,68,.8);color:#fff;border:none;">
                    <span class="sa-btn-spinner" id="spin-elim"></span>
                    <span id="txt-elim">Sí, eliminar</span>
                </button>
            </div>
        </div>
    </div>
</div>


{{-- Estilos específicos --}}
<style>
.sa-modal-input {
    background: rgba(255,255,255,.06) !important;
    border: 1px solid rgba(255,255,255,.12) !important;
    color: #fff !important;
    border-radius: 10px !important;
    transition: border-color .18s, box-shadow .18s;
}
.sa-modal-input:focus {
    border-color: rgba(212,160,23,.5) !important;
    box-shadow: 0 0 0 3px rgba(212,160,23,.12) !important;
    background: rgba(255,255,255,.09) !important;
    outline: none;
}
.sa-modal-input::placeholder { color: rgba(255,255,255,.25) !important; }
.sa-modal-input option { background: #1e293b; color: #fff; }

.sa-field-error {
    font-size: .75rem;
    color: #f87171;
    margin-top: .25rem;
    min-height: 1rem;
}

.sa-modal-tab.active {
    color: var(--sa-gold) !important;
    border-bottom-color: var(--sa-gold) !important;
}

.sa-btn-spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255,255,255,.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: sa-spin .6s linear infinite;
    vertical-align: middle;
    margin-right: 6px;
    display: none;
}

@keyframes sa-spin { to { transform: rotate(360deg); } }
</style>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');

    // ── Buscador local ────────────────────────────────────────────────────
    $('#buscador').on('keyup', function () {
        const txt = $(this).val().toLowerCase();
        $('#tablaPacientes tbody tr').each(function () {
            $(this).toggle(!txt || $(this).data('busqueda').includes(txt));
        });
    });

    $('#filtroCentro').on('change', function () {
        const centroId = $(this).val();
        const params   = new URLSearchParams();
        if (centroId) params.set('centro_id', centroId);
        params.set('page', '1');
        window.location.href = `{{ route('superadmin.pacientes.index') }}?${params.toString()}`;
    });

    $('#btnLimpiar').on('click', function () {
        window.location.href = `{{ route('superadmin.pacientes.index') }}`;
    });

    // ── Toggle contraseñas ────────────────────────────────────────────────
    function makeToggle(btnId, inputId, eyeId) {
        $('#' + btnId).on('click', function () {
            const t = $('#' + inputId).attr('type') === 'password' ? 'text' : 'password';
            $('#' + inputId).attr('type', t);
            $('#' + eyeId).toggleClass('bi-eye-fill bi-eye-slash-fill');
        });
    }
    makeToggle('togglePacPass', 'pac_password', 'eyePacPass');
    makeToggle('togglePacPassConfirm', 'pac_password_confirm', 'eyePacPassConfirm');

    // ── Formato RUT ───────────────────────────────────────────────────────
    function formatRut(rut) {
        const limpio = rut.replace(/[^0-9kK]/g, '');
        if (limpio.length < 2) return limpio;
        return limpio.slice(0, -1) + '-' + limpio.slice(-1).toUpperCase();
    }
    $('#pac_rut').on('input', function () { this.value = formatRut(this.value); });

    // ── Sistema de tabs ───────────────────────────────────────────────────
    $(document).on('click', '.sa-modal-tab', function () {
        const tab = $(this).data('tab');

        // Actualizar estilos de tabs
        $('.sa-modal-tab').removeClass('active').css({
            'color': 'rgba(255,255,255,.5)',
            'border-bottom-color': 'transparent'
        });
        $(this).addClass('active').css({
            'color': 'var(--sa-gold)',
            'border-bottom-color': 'var(--sa-gold)'
        });

        // Mostrar/ocultar paneles
        $('.sa-tab-panel').addClass('d-none');
        $('#tab-' + tab).removeClass('d-none');

        // Mostrar/ocultar botón guardar según tab
        // En tab "centro" tiene su propio botón, ocultamos el genérico
        if (tab === 'centro') {
            $('#btnGuardarPaciente').hide();
        } else {
            $('#btnGuardarPaciente').show();
        }
    });

    // ── Limpiar errores ───────────────────────────────────────────────────
    function clearErrors() {
        $('.sa-field-error').text('');
    }

    function setSpinner(spinnerId, textId, loading, text) {
        $('#' + spinnerId).css('display', loading ? 'inline-block' : 'none');
        $('#' + textId).text(loading ? 'Guardando...' : text);
    }

    // ── ABRIR MODAL VER/EDITAR ────────────────────────────────────────────
    $(document).on('click', '.btn-ver-paciente', function () {
        const btn = $(this);

        clearErrors();
        $('#pac_password').val('');
        $('#pac_password_confirm').val('');

        // Llenar datos
        $('#pac_id').val(btn.data('id'));
        $('#pac_name').val(btn.data('name'));
        $('#pac_apellidos').val(btn.data('apellidos'));
        $('#pac_email').val(btn.data('email'));
        $('#pac_rut').val(btn.data('rut'));
        $('#pac_telefono').val(btn.data('telefono'));
        $('#pac_activo').val(btn.data('activo').toString());
        $('#pac_centro').val(btn.data('centro'));
        $('#centro-actual-label').text(btn.data('centro-nombre') || 'Sin centro asignado');

        // Avatar y título
        const inicial = (btn.data('name') || 'P').charAt(0).toUpperCase();
        $('#modal-avatar').text(inicial);
        $('#modal-titulo').text(btn.data('name') + ' ' + (btn.data('apellidos') || ''));
        $('#modal-subtitulo').text(btn.data('email'));

        // Activar primer tab
        $('.sa-modal-tab').first().trigger('click');
        $('#btnGuardarPaciente').show();

        new bootstrap.Modal(document.getElementById('modalVerPaciente')).show();
    });

    // ── GUARDAR DATOS PERSONALES / ACCESO ─────────────────────────────────
    $('#btnGuardarPaciente').on('click', function () {
        clearErrors();

        const id         = $('#pac_id').val();
        const name       = $('#pac_name').val().trim();
        const apellidos  = $('#pac_apellidos').val().trim();
        const email      = $('#pac_email').val().trim();
        const rut        = $('#pac_rut').val().trim();
        const telefono   = $('#pac_telefono').val().trim();
        const activo     = $('#pac_activo').val();
        const password   = $('#pac_password').val();
        const passConfirm= $('#pac_password_confirm').val();

        // Validación básica
        let hayError = false;
        if (!name) { $('#err_name').text('El nombre es obligatorio.'); hayError = true; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
            $('#err_email').text('Ingresa un email válido.'); hayError = true;
        }
        if (password && password.length < 6) {
            $('#err_password').text('La contraseña debe tener al menos 6 caracteres.');
            // Ir al tab de acceso para mostrar el error
            $('.sa-modal-tab[data-tab="acceso"]').trigger('click');
            hayError = true;
        }
        if (password && password !== passConfirm) {
            $('#err_password_confirm').text('Las contraseñas no coinciden.');
            $('.sa-modal-tab[data-tab="acceso"]').trigger('click');
            hayError = true;
        }
        if (hayError) return;

        const datos = { _token: csrf, _method: 'PUT', name, Apellidos: apellidos, email, Rut: rut, telefono, activo };
        if (password) { datos.password = password; datos.password_confirmation = passConfirm; }

        setSpinner('spin-guardar', 'txt-guardar', true, 'Guardar cambios');
        $('#btnGuardarPaciente').prop('disabled', true);

        $.ajax({
            url: `/superadmin/pacientes/${id}`,
            method: 'POST',
            data: datos,
            success: function (res) {
                saToast(res.message || 'Paciente actualizado correctamente.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalVerPaciente')).hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                const errores = xhr.responseJSON?.errors;
                if (errores) {
                    if (errores.name)     $('#err_name').text(errores.name[0]);
                    if (errores.Apellidos)$('#err_apellidos').text(errores.Apellidos[0]);
                    if (errores.email)    $('#err_email').text(errores.email[0]);
                    if (errores.Rut)      $('#err_rut').text(errores.Rut[0]);
                    if (errores.telefono) $('#err_telefono').text(errores.telefono[0]);
                    if (errores.password) {
                        $('#err_password').text(errores.password[0]);
                        $('.sa-modal-tab[data-tab="acceso"]').trigger('click');
                    }
                    saToast('Corrige los errores indicados.', 'danger');
                } else {
                    saToast(xhr.responseJSON?.message ?? 'Error al guardar.', 'danger');
                }
            },
            complete: function () {
                setSpinner('spin-guardar', 'txt-guardar', false, 'Guardar cambios');
                $('#btnGuardarPaciente').prop('disabled', false);
            }
        });
    });

    // ── CAMBIAR CENTRO (desde modal detalle, tab centro) ──────────────────
    $('#btnConfirmarCentro').on('click', function () {
        $('#err_centro').text('');
        const id     = $('#pac_id').val();
        const centro = $('#pac_centro').val();

        if (!centro) { $('#err_centro').text('Debes seleccionar un centro.'); return; }

        setSpinner('spin-centro', 'txt-centro', true, 'Confirmar cambio de centro');
        $(this).prop('disabled', true);

        $.ajax({
            url: `/superadmin/pacientes/${id}/cambiar-centro`,
            method: 'POST',
            data: { _token: csrf, centro_medico_id: centro },
            success: function (res) {
                saToast('Centro médico actualizado correctamente.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalVerPaciente')).hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                $('#err_centro').text(xhr.responseJSON?.message ?? 'Error al cambiar el centro.');
                saToast(xhr.responseJSON?.message ?? 'Error al cambiar el centro.', 'danger');
            },
            complete: function () {
                setSpinner('spin-centro', 'txt-centro', false, 'Confirmar cambio de centro');
                $('#btnConfirmarCentro').prop('disabled', false);
            }
        });
    });

    // ── MODAL CAMBIO RÁPIDO DE CENTRO (botón directo en tabla) ────────────
    $(document).on('click', '.btn-cambiar-centro', function () {
        const btn = $(this);
        $('#rapido_pac_id').val(btn.data('id'));
        $('#rapido-nombre').text(btn.data('name'));
        $('#rapido-centro-actual').text(btn.data('centro-nombre') || 'Sin centro');
        $('#rapido_centro').val('');
        $('#err_rapido_centro').text('');

        const inicial = (btn.data('name') || 'P').charAt(0).toUpperCase();
        $('#rapido-avatar').text(inicial);

        // Deshabilitar opción del centro actual
        $('#rapido_centro option').prop('disabled', false).text(function () {
            return $(this).text().replace(' (actual)', '');
        });
        const centroActualId = btn.data('centro');
        if (centroActualId) {
            $(`#rapido_centro option[value="${centroActualId}"]`)
                .prop('disabled', true)
                .text(function () { return $(this).text() + ' (actual)'; });
        }

        new bootstrap.Modal(document.getElementById('modalCambioCentroRapido')).show();
    });

    $('#btnConfirmarCentroRapido').on('click', function () {
        $('#err_rapido_centro').text('');
        const id     = $('#rapido_pac_id').val();
        const centro = $('#rapido_centro').val();

        if (!centro) { $('#err_rapido_centro').text('Debes seleccionar un nuevo centro.'); return; }

        setSpinner('spin-rapido', 'txt-rapido', true, 'Confirmar cambio');
        $(this).prop('disabled', true);

        $.ajax({
            url: `/superadmin/pacientes/${id}/cambiar-centro`,
            method: 'POST',
            data: { _token: csrf, centro_medico_id: centro },
            success: function () {
                saToast('Centro médico actualizado.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalCambioCentroRapido')).hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                $('#err_rapido_centro').text(xhr.responseJSON?.message ?? 'Error al cambiar el centro.');
            },
            complete: function () {
                setSpinner('spin-rapido', 'txt-rapido', false, 'Confirmar cambio');
                $('#btnConfirmarCentroRapido').prop('disabled', false);
            }
        });
    });

    // ── MODAL ELIMINAR ────────────────────────────────────────────────────
    $(document).on('click', '.btn-eliminar-paciente', function () {
        const btn = $(this);
        $('#elim_pac_id').val(btn.data('id'));
        $('#elim_pac_nombre').text(btn.data('name'));
        new bootstrap.Modal(document.getElementById('modalEliminarPaciente')).show();
    });

    $('#btnConfirmarEliminar').on('click', function () {
        const id = $('#elim_pac_id').val();

        setSpinner('spin-elim', 'txt-elim', true, 'Sí, eliminar');
        $(this).prop('disabled', true);

        $.ajax({
            url: `/superadmin/pacientes/${id}`,
            method: 'POST',
            data: { _token: csrf, _method: 'DELETE' },
            success: function () {
                saToast('Paciente eliminado correctamente.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalEliminarPaciente')).hide();
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                saToast(xhr.responseJSON?.message ?? 'Error al eliminar.', 'danger');
            },
            complete: function () {
                setSpinner('spin-elim', 'txt-elim', false, 'Sí, eliminar');
                $('#btnConfirmarEliminar').prop('disabled', false);
            }
        });
    });
});
</script>
@endsection