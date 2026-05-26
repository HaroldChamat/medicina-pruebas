@extends('superadmin.superadmin')

@section('page-title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:#64748b;">Dashboard</li>
@endsection

@section('content')

<div class="row g-4 mb-5">

    {{-- Centros --}}
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="icon-wrap" style="background:rgba(212,160,23,.12);">
                    <i class="bi bi-building-fill" style="color:var(--sa-gold);"></i>
                </div>
                <span class="badge rounded-pill" style="background:rgba(212,160,23,.12); color:var(--sa-gold); font-size:.7rem;">Total</span>
            </div>
            <div class="stat-value">{{ $stats['centros'] }}</div>
            <div class="stat-label">Centros médicos</div>
        </div>
    </div>

    {{-- Admins --}}
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="icon-wrap" style="background:rgba(99,102,241,.12);">
                    <i class="bi bi-shield-fill-check" style="color:#6366f1;"></i>
                </div>
                <span class="badge rounded-pill" style="background:rgba(99,102,241,.12); color:#6366f1; font-size:.7rem;">Total</span>
            </div>
            <div class="stat-value">{{ $stats['admins'] }}</div>
            <div class="stat-label">Administradores</div>
        </div>
    </div>

    {{-- Médicos --}}
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="icon-wrap" style="background:rgba(16,185,129,.12);">
                    <i class="bi bi-person-badge-fill" style="color:#10b981;"></i>
                </div>
                <span class="badge rounded-pill" style="background:rgba(16,185,129,.12); color:#10b981; font-size:.7rem;">Total</span>
            </div>
            <div class="stat-value">{{ $stats['medicos'] }}</div>
            <div class="stat-label">Médicos</div>
        </div>
    </div>

    {{-- Pacientes --}}
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="icon-wrap" style="background:rgba(59,130,246,.12);">
                    <i class="bi bi-person-heart" style="color:#3b82f6;"></i>
                </div>
                <span class="badge rounded-pill" style="background:rgba(59,130,246,.12); color:#3b82f6; font-size:.7rem;">Total</span>
            </div>
            <div class="stat-value">{{ $stats['pacientes'] }}</div>
            <div class="stat-label">Pacientes</div>
        </div>
    </div>

    {{-- Citas --}}
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="icon-wrap" style="background:rgba(245,158,11,.12);">
                    <i class="bi bi-calendar-week-fill" style="color:#f59e0b;"></i>
                </div>
                <span class="badge rounded-pill" style="background:rgba(245,158,11,.12); color:#f59e0b; font-size:.7rem;">Total</span>
            </div>
            <div class="stat-value">{{ $stats['citas'] }}</div>
            <div class="stat-label">Citas totales</div>
        </div>
    </div>

    {{-- Tickets --}}
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="icon-wrap" style="background:rgba(239,68,68,.12);">
                    <i class="bi bi-ticket-detailed-fill" style="color:#ef4444;"></i>
                </div>
                <span class="badge rounded-pill" style="background:rgba(239,68,68,.12); color:#ef4444; font-size:.7rem;">Total</span>
            </div>
            <div class="stat-value">{{ $stats['tickets'] }}</div>
            <div class="stat-label">Tickets soporte</div>
        </div>
    </div>
</div>

{{-- Tabla de centros --}}
<div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
    <div class="d-flex align-items-center justify-content-between px-4 py-3"
         style="background: var(--sa-dark); border-bottom: 1px solid var(--sa-border);">
        <div>
            <h6 class="text-white fw-bold mb-0">
                <i class="bi bi-building-fill me-2" style="color:var(--sa-gold);"></i>
                Resumen por Centro Médico
            </h6>
        </div>
        <a href="{{ route('superadmin.centros.index') }}"
           class="btn btn-sm rounded-pill"
           style="background:rgba(212,160,23,.15); color:var(--sa-gold); border:1px solid rgba(212,160,23,.3);">
            Ver todos
        </a>
    </div>
    <div class="table-responsive">
        <table class="table sa-table mb-0">
            <thead>
                <tr>
                    <th>Centro</th>
                    <th>Dirección</th>
                    <th class="text-center">Admins</th>
                    <th class="text-center">Médicos</th>
                    <th class="text-center">Pacientes</th>
                    <th class="text-center">Total usuarios</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($centros as $centro)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:8px; height:8px; border-radius:50%; background:var(--sa-gold); flex-shrink:0;"></div>
                                <span class="fw-semibold">{{ $centro->nombre }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $centro->direccion }}</td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(99,102,241,.15); color:#6366f1;">
                                {{ $centro->total_admins }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(16,185,129,.15); color:#10b981;">
                                {{ $centro->total_medicos }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(59,130,246,.15); color:#3b82f6;">
                                {{ $centro->total_pacientes }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $centro->total_usuarios }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('superadmin.centros.show', $centro->id) }}"
                               class="btn btn-sm rounded-pill"
                               style="background:rgba(212,160,23,.1); color:var(--sa-gold); border:1px solid rgba(212,160,23,.2);">
                                <i class="bi bi-eye me-1"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-building-slash me-2"></i>No hay centros registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection