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
            <div class="fw-bold fs-4" style="color:var(--sa-gold);">{{ $admins->count() }}</div>
            <small class="opacity-75">Admins</small>
        </div>
        <div class="text-center">
            <div class="fw-bold fs-4" style="color:#10b981;">{{ $medicos->count() }}</div>
            <small class="opacity-75">Médicos</small>
        </div>
        <div class="text-center">
            <div class="fw-bold fs-4" style="color:#3b82f6;">{{ $pacientes->count() }}</div>
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
                data-bs-toggle="tab" data-bs-target="#tabAdmins">
            <i class="bi bi-shield-check me-1"></i>Admins ({{ $admins->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $tabActiva === 'medicos' ? 'active' : '' }}"
                data-bs-toggle="tab" data-bs-target="#tabMedicos">
            <i class="bi bi-person-badge me-1"></i>Médicos ({{ $medicos->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $tabActiva === 'pacientes' ? 'active' : '' }}"
                data-bs-toggle="tab" data-bs-target="#tabPacientes">
            <i class="bi bi-person-heart me-1"></i>Pacientes ({{ $pacientes->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ $tabActiva === 'citas' ? 'active' : '' }}"
                data-bs-toggle="tab" data-bs-target="#tabCitas">
            <i class="bi bi-calendar-week me-1"></i>Citas
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- Admins --}}
    <div class="tab-pane fade {{ $tabActiva === 'admins' ? 'show active' : '' }}" id="tabAdmins">
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th><th>Email</th><th>RUT</th><th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td class="fw-semibold">{{ $admin->name }} {{ $admin->Apellidos }}</td>
                                <td class="text-muted small">{{ $admin->email }}</td>
                                <td><span class="badge rounded-pill" style="background:rgba(212,160,23,.1);color:var(--sa-gold);">{{ $admin->Rut }}</span></td>
                                <td class="text-muted small">{{ $admin->telefono ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin administradores</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Médicos --}}
    <div class="tab-pane fade {{ $tabActiva === 'medicos' ? 'show active' : '' }}" id="tabMedicos">
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th><th>Email</th><th>Especialidades</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicos as $medico)
                            <tr>
                                <td class="fw-semibold">{{ $medico->name }} {{ $medico->Apellidos }}</td>
                                <td class="text-muted small">{{ $medico->email }}</td>
                                <td>
                                    @foreach($medico->especialidades as $esp)
                                        <span class="badge bg-primary me-1">{{ $esp->Nombre_especialidad }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($medico->activo)
                                        <span class="badge" style="background:rgba(16,185,129,.15);color:#10b981;">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin médicos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pacientes --}}
    <div class="tab-pane fade {{ $tabActiva === 'pacientes' ? 'show active' : '' }}" id="tabPacientes">
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr><th>Nombre</th><th>Email</th><th>RUT</th><th>Teléfono</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pacientes as $paciente)
                            <tr>
                                <td class="fw-semibold">{{ $paciente->name }} {{ $paciente->Apellidos }}</td>
                                <td class="text-muted small">{{ $paciente->email }}</td>
                                <td><span class="badge rounded-pill" style="background:rgba(59,130,246,.1);color:#3b82f6;">{{ $paciente->Rut }}</span></td>
                                <td class="text-muted small">{{ $paciente->telefono ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin pacientes</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Citas --}}
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
                        <tr><th>Código</th><th>Médico</th><th>Paciente</th><th>Fecha</th><th>Prestación</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        @forelse($citas as $cita)
                            <tr>
                                <td><span class="badge bg-secondary" style="font-size:.7rem;">{{ $cita->codigo_cita ?? 'CIT-'.$cita->id }}</span></td>
                                <td class="small">{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</td>
                                <td class="small">{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}</td>
                                <td class="small">{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</td>
                                <td class="small">{{ $cita->prestacion->nombre ?? '—' }}</td>
                                <td>
                                    @php
                                        $estadoColors = ['Pendiente'=>'warning','Programada'=>'primary','Finalizada'=>'success','Cancelada'=>'danger'];
                                        $color = $estadoColors[$cita->estado] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $cita->estado }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Sin citas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginación manual (sin el renderizado de Laravel) --}}
        @if($citas->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="text-muted small">
                    Mostrando {{ $citas->firstItem() }}–{{ $citas->lastItem() }}
                    de {{ $citas->total() }} citas
                </div>
                <div class="sa-pagination">
                    {{-- Anterior --}}
                    @if($citas->onFirstPage())
                        <span class="sa-page-btn disabled">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $citas->previousPageUrl() }}&tab=citas{{ $filtroEstado ? '&estado='.$filtroEstado : '' }}"
                           class="sa-page-btn">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Páginas --}}
                    @php
                        $start = max(1, $citas->currentPage() - 2);
                        $end   = min($citas->lastPage(), $citas->currentPage() + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $citas->url(1) }}&tab=citas{{ $filtroEstado ? '&estado='.$filtroEstado : '' }}"
                           class="sa-page-btn">1</a>
                        @if($start > 2)
                            <span class="sa-page-btn disabled">…</span>
                        @endif
                    @endif

                    @for($p = $start; $p <= $end; $p++)
                        <a href="{{ $citas->url($p) }}&tab=citas{{ $filtroEstado ? '&estado='.$filtroEstado : '' }}"
                           class="sa-page-btn {{ $citas->currentPage() === $p ? 'active' : '' }}">
                            {{ $p }}
                        </a>
                    @endfor

                    @if($end < $citas->lastPage())
                        @if($end < $citas->lastPage() - 1)
                            <span class="sa-page-btn disabled">…</span>
                        @endif
                        <a href="{{ $citas->url($citas->lastPage()) }}&tab=citas{{ $filtroEstado ? '&estado='.$filtroEstado : '' }}"
                           class="sa-page-btn">{{ $citas->lastPage() }}</a>
                    @endif

                    {{-- Siguiente --}}
                    @if($citas->hasMorePages())
                        <a href="{{ $citas->nextPageUrl() }}&tab=citas{{ $filtroEstado ? '&estado='.$filtroEstado : '' }}"
                           class="sa-page-btn">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <span class="sa-page-btn disabled">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</div>

<style>
/* Paginación personalizada para el superadmin */
.sa-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
}
.sa-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    color: #64748b;
    background: #fff;
    border: 1px solid #e2e8f0;
    transition: all .15s;
    cursor: pointer;
    line-height: 1;
}
.sa-page-btn:hover:not(.disabled):not(.active) {
    background: #f1f5f9;
    color: var(--sa-dark, #0a1628);
    border-color: #cbd5e1;
}
.sa-page-btn.active {
    background: var(--sa-blue, #0d3b6e);
    color: #fff;
    border-color: var(--sa-blue, #0d3b6e);
}
.sa-page-btn.disabled {
    opacity: .4;
    cursor: default;
    pointer-events: none;
}
/* Asegurar que los íconos bi no se agranden */
.sa-page-btn i {
    font-size: .8rem;
    line-height: 1;
}
</style>

@endsection

@section('scripts')
<script>
// Filtro de estado: conserva el tab activo y la página 1
$('#filtroEstadoCitas').on('change', function () {
    const estado = $(this).val();
    const url    = new URL(window.location.href);
    url.searchParams.set('tab', 'citas');
    url.searchParams.set('page', '1');
    if (estado) url.searchParams.set('estado', estado);
    else url.searchParams.delete('estado');
    window.location.href = url.toString();
});
</script>
@endsection