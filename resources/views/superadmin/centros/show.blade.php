@extends('superadmin.superadmin')

@section('page-title', $centro->nombre)
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('superadmin.centros.index') }}" class="text-decoration-none" style="color:#64748b;">Centros</a>
    </li>
    <li class="breadcrumb-item active" style="color:#64748b;">{{ $centro->nombre }}</li>
@endsection

@section('content')

{{-- Header del centro --}}
<div class="rounded-3 p-4 mb-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3"
     style="background:linear-gradient(135deg,#0d2240,#0d3b6e);">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('superadmin.centros.index') }}"
           class="btn btn-sm rounded-pill flex-shrink-0"
           style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-building-fill me-2" style="color:var(--sa-gold);"></i>{{ $centro->nombre }}</h4>
            <small class="opacity-75"><i class="bi bi-geo-alt me-1"></i>{{ $centro->direccion }}</small>
        </div>
    </div>
    <div class="d-flex gap-3 flex-wrap">
        <div class="text-center">
            <div class="fw-bold fs-4" style="color:var(--sa-gold);">{{ $admins->total() }}</div>
            <small class="opacity-75">Admins</small>
        </div>
        <div class="text-center">
            <div class="fw-bold fs-4" style="color:#10b981;">{{ $medicos->total() }}</div>
            <small class="opacity-75">Médicos</small>
        </div>
        <div class="text-center">
            <div class="fw-bold fs-4" style="color:#3b82f6;">{{ $pacientes->total() }}</div>
            <small class="opacity-75">Pacientes</small>
        </div>
        <div class="text-center">
            <div class="fw-bold fs-4" style="color:#f59e0b;">{{ $citas->total() }}</div>
            <small class="opacity-75">Citas</small>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="centroTabs">
    <li class="nav-item">
        <button class="nav-link {{ $tabActiva === 'admins' ? 'active' : '' }}"
                data-bs-toggle="tab" data-bs-target="#tabAdmins" data-tab="admins">
            <i class="bi bi-shield-check me-1"></i>Admins ({{ $admins->total() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $tabActiva === 'medicos' ? 'active' : '' }}"
                data-bs-toggle="tab" data-bs-target="#tabMedicos" data-tab="medicos">
            <i class="bi bi-person-badge me-1"></i>Médicos ({{ $medicos->total() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $tabActiva === 'pacientes' ? 'active' : '' }}"
                data-bs-toggle="tab" data-bs-target="#tabPacientes" data-tab="pacientes">
            <i class="bi bi-person-heart me-1"></i>Pacientes ({{ $pacientes->total() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $tabActiva === 'citas' ? 'active' : '' }}"
                data-bs-toggle="tab" data-bs-target="#tabCitas" data-tab="citas">
            <i class="bi bi-calendar-week me-1"></i>Citas
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- ══ TAB ADMINS ══ --}}
    <div class="tab-pane fade {{ $tabActiva === 'admins' ? 'show active' : '' }}" id="tabAdmins">
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Nombre</th><th>Email</th><th>RUT</th><th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td class="text-muted">{{ ($admins->currentPage() - 1) * $admins->perPage() + $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $admin->name }} {{ $admin->Apellidos }}</td>
                                <td class="text-muted small">{{ $admin->email }}</td>
                                <td>
                                    <span class="badge rounded-pill"
                                          style="background:rgba(212,160,23,.1);color:var(--sa-gold);">
                                        {{ $admin->Rut }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $admin->telefono ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-shield-slash fs-2 d-block mb-2"></i>
                                    Sin administradores
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($admins->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3"
                     style="border-top:1px solid #e2e8f0;">
                    <div class="text-muted small">
                        Mostrando {{ $admins->firstItem() }}–{{ $admins->lastItem() }}
                        de {{ $admins->total() }} administradores
                    </div>
                    @include('superadmin.partials.pagination', ['paginator' => $admins])
                </div>
            @endif
        </div>
    </div>

    {{-- ══ TAB MÉDICOS ══ --}}
    <div class="tab-pane fade {{ $tabActiva === 'medicos' ? 'show active' : '' }}" id="tabMedicos">
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Nombre</th><th>Email</th><th>Especialidades</th><th>Horario</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicos as $medico)
                            <tr>
                                <td class="text-muted">{{ ($medicos->currentPage() - 1) * $medicos->perPage() + $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $medico->name }} {{ $medico->Apellidos }}</td>
                                <td class="text-muted small">{{ $medico->email }}</td>
                                <td>
                                    @forelse($medico->especialidades as $esp)
                                        <span class="badge bg-primary me-1" style="font-size:.7rem;">
                                            {{ $esp->Nombre_especialidad }}
                                        </span>
                                    @empty
                                        <span class="text-muted small fst-italic">Sin especialidad</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if($medico->horario)
                                        <span class="badge" style="background:rgba(59,130,246,.12);color:#3b82f6;font-size:.7rem;">
                                            {{ substr($medico->horario->hora_inicio,0,5) }} – {{ substr($medico->horario->hora_fin,0,5) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size:.7rem;">Sin horario</span>
                                    @endif
                                </td>
                                <td>
                                    @if($medico->activo)
                                        <span class="badge rounded-pill"
                                              style="background:rgba(16,185,129,.15);color:#10b981;">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.5rem;"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">
                                            <i class="bi bi-circle me-1" style="font-size:.5rem;"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-person-badge fs-2 d-block mb-2"></i>
                                    Sin médicos
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($medicos->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3"
                     style="border-top:1px solid #e2e8f0;">
                    <div class="text-muted small">
                        Mostrando {{ $medicos->firstItem() }}–{{ $medicos->lastItem() }}
                        de {{ $medicos->total() }} médicos
                    </div>
                    @include('superadmin.partials.pagination', ['paginator' => $medicos])
                </div>
            @endif
        </div>
    </div>

    {{-- ══ TAB PACIENTES ══ --}}
    <div class="tab-pane fade {{ $tabActiva === 'pacientes' ? 'show active' : '' }}" id="tabPacientes">
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Nombre</th><th>Email</th><th>RUT</th><th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pacientes as $paciente)
                            <tr>
                                <td class="text-muted">{{ ($pacientes->currentPage() - 1) * $pacientes->perPage() + $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $paciente->name }} {{ $paciente->Apellidos }}</td>
                                <td class="text-muted small">{{ $paciente->email }}</td>
                                <td>
                                    <span class="badge rounded-pill"
                                          style="background:rgba(59,130,246,.1);color:#3b82f6;">
                                        {{ $paciente->Rut }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $paciente->telefono ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-person-heart fs-2 d-block mb-2"></i>
                                    Sin pacientes
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
    </div>

    {{-- ══ TAB CITAS ══ --}}
    <div class="tab-pane fade {{ $tabActiva === 'citas' ? 'show active' : '' }}" id="tabCitas">
        <div class="d-flex gap-2 mb-3">
            <select id="filtroEstadoCitas" class="form-select form-select-sm w-auto">
                <option value="">Todos los estados</option>
                <option value="Pendiente"  {{ $filtroEstado === 'Pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="Programada" {{ $filtroEstado === 'Programada' ? 'selected' : '' }}>Programada</option>
                <option value="Finalizada" {{ $filtroEstado === 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                <option value="Cancelada"  {{ $filtroEstado === 'Cancelada'  ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Código</th><th>Médico</th><th>Paciente</th>
                            <th>Fecha</th><th>Prestación</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citas as $cita)
                            <tr>
                                <td class="text-muted">{{ ($citas->currentPage() - 1) * $citas->perPage() + $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-secondary" style="font-size:.7rem;">
                                        {{ $cita->codigo_cita ?? 'CIT-'.$cita->id }}
                                    </span>
                                </td>
                                <td class="small">{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</td>
                                <td class="small">{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}</td>
                                <td class="small">
                                    {{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}
                                </td>
                                <td class="small">{{ $cita->prestacion->nombre ?? '—' }}</td>
                                <td>
                                    @php
                                        $estadoColors = [
                                            'Pendiente'  => 'warning',
                                            'Programada' => 'primary',
                                            'Finalizada' => 'success',
                                            'Cancelada'  => 'danger',
                                        ];
                                        $color = $estadoColors[$cita->estado] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $cita->estado }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                    Sin citas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($citas->hasPages())
                <div class="d-flex justify-content-between align-items-center px-4 py-3"
                     style="border-top:1px solid #e2e8f0;">
                    <div class="text-muted small">
                        Mostrando {{ $citas->firstItem() }}–{{ $citas->lastItem() }}
                        de {{ $citas->total() }} citas
                    </div>
                    @include('superadmin.partials.pagination', ['paginator' => $citas])
                </div>
            @endif
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Al cargar la página, activar el tab que dice la URL ───────────────
    const tabActiva = '{{ $tabActiva }}';
    const tabMap = {
        admins:    '#tabAdmins',
        medicos:   '#tabMedicos',
        pacientes: '#tabPacientes',
        citas:     '#tabCitas',
    };

    if (tabActiva && tabMap[tabActiva]) {
        // Buscar el botón del tab correcto y activarlo con Bootstrap
        const boton = document.querySelector(`[data-bs-target="${tabMap[tabActiva]}"]`);
        if (boton) {
            const tabInstancia = new bootstrap.Tab(boton);
            tabInstancia.show();
        }
    }

    // ── 2. Al cambiar de tab manualmente, actualizar la URL sin recargar ─────
    document.querySelectorAll('#centroTabs .nav-link').forEach(function (btn) {
        btn.addEventListener('shown.bs.tab', function () {
            const tabNombre = this.getAttribute('data-tab');
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabNombre);
            window.history.replaceState(null, '', url.toString());
        });
    });

    // ── 3. Todos los links de paginación inyectan el tab activo en su URL ────
    document.querySelectorAll('.sa-pagination a').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Leer el tab activo en este momento
            const tabActual = new URL(window.location.href).searchParams.get('tab') || 'admins';

            // Construir la URL de destino con el tab correcto
            const destino = new URL(this.href, window.location.href);
            destino.searchParams.set('tab', tabActual);

            window.location.href = destino.toString();
        });
    });

    // ── 4. Filtro de estado de citas ─────────────────────────────────────────
    const filtroEstado = document.getElementById('filtroEstadoCitas');
    if (filtroEstado) {
        filtroEstado.addEventListener('change', function () {
            const estado = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'citas');
            url.searchParams.set('citas_page', '1');
            if (estado) url.searchParams.set('estado', estado);
            else url.searchParams.delete('estado');
            window.location.href = url.toString();
        });
    }

});
</script>
@endsection