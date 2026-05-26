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
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-building-fill me-2" style="color:var(--sa-gold);"></i>{{ $centro->nombre }}</h4>
        <small class="opacity-75"><i class="bi bi-geo-alt me-1"></i>{{ $centro->direccion }}</small>
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
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabAdmins">
            <i class="bi bi-shield-check me-1"></i>Admins ({{ $admins->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMedicos">
            <i class="bi bi-person-badge me-1"></i>Médicos ({{ $medicos->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPacientes">
            <i class="bi bi-person-heart me-1"></i>Pacientes ({{ $pacientes->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCitas">
            <i class="bi bi-calendar-week me-1"></i>Citas
        </button>
    </li>
</ul>

<div class="tab-content">

    {{-- Admins --}}
    <div class="tab-pane fade show active" id="tabAdmins">
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
    <div class="tab-pane fade" id="tabMedicos">
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
    <div class="tab-pane fade" id="tabPacientes">
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
    <div class="tab-pane fade" id="tabCitas">
        <div class="d-flex gap-2 mb-3">
            <select id="filtroEstadoCitas" class="form-select form-select-sm w-auto">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Programada">Programada</option>
                <option value="Finalizada">Finalizada</option>
                <option value="Cancelada">Cancelada</option>
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
        {{-- Paginación --}}
        @if($citas->hasPages())
            <div class="mt-3">{{ $citas->links() }}</div>
        @endif
    </div>

</div>

@endsection

@section('scripts')
<script>
$('#filtroEstadoCitas').on('change', function () {
    const estado = $(this).val();
    const url = new URL(window.location.href);
    if (estado) url.searchParams.set('estado', estado);
    else url.searchParams.delete('estado');
    window.location.href = url.toString();
});
</script>
@endsection