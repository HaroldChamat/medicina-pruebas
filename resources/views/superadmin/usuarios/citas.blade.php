@extends('superadmin.superadmin')

@section('page-title', 'Citas')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:#64748b;">Citas</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <p class="text-muted mb-0 small">
        Todas las citas del sistema.
        <strong>{{ $citas->total() }}</strong> en total.
    </p>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
    <div class="card-body py-3 px-4">
        <div class="row g-2 align-items-center">
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
            <div class="col-md-4">
                <select id="filtroEstado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente"  {{ $filtroEstado === 'Pendiente'  ? 'selected' : '' }}>Pendiente</option>
                    <option value="Programada" {{ $filtroEstado === 'Programada' ? 'selected' : '' }}>Programada</option>
                    <option value="Finalizada" {{ $filtroEstado === 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                    <option value="Cancelada"  {{ $filtroEstado === 'Cancelada'  ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-sm btn-outline-secondary w-100 rounded-pill" id="btnLimpiar">
                    <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Resumen por estado --}}
<div class="row g-3 mb-4">
    @php
        $totales = [
            'Pendiente'  => ['color'=>'warning', 'icon'=>'bi-hourglass',       'label'=>'Pendientes'],
            'Programada' => ['color'=>'primary',  'icon'=>'bi-calendar-check', 'label'=>'Programadas'],
            'Finalizada' => ['color'=>'success',  'icon'=>'bi-check-circle',   'label'=>'Finalizadas'],
            'Cancelada'  => ['color'=>'danger',   'icon'=>'bi-x-circle',       'label'=>'Canceladas'],
        ];
    @endphp
    @foreach($totales as $estado => $cfg)
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <i class="bi {{ $cfg['icon'] }} fs-3 mb-1 text-{{ $cfg['color'] }}"></i>
                <div class="fw-bold fs-5">
                    {{ $citas->where('estado', $estado)->count() }}
                </div>
                <small class="text-muted">{{ $cfg['label'] }}</small>
            </div>
        </div>
    @endforeach
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <div class="table-responsive">
        <table class="table sa-table mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Médico</th>
                    <th>Paciente</th>
                    <th>Prestación</th>
                    <th>Fecha y hora</th>
                    <th>Centro</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citas as $cita)
                    <tr>
                        <td>
                            <span class="badge bg-secondary" style="font-size:.7rem;letter-spacing:.5px;">
                                {{ $cita->codigo_cita ?? 'CIT-'.$cita->id }}
                            </span>
                        </td>
                        <td class="small fw-semibold">{{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</td>
                        <td class="small">{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}</td>
                        <td>
                            @if($cita->prestacion)
                                <span class="badge" style="background:rgba(99,102,241,.15);color:#6366f1;font-size:.7rem;">
                                    {{ $cita->prestacion->nombre }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="small">{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($cita->medico->centroMedico)
                                <span class="badge rounded-pill"
                                      style="background:rgba(16,185,129,.12);color:#10b981;font-size:.7rem;">
                                    {{ $cita->medico->centroMedico->nombre }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $colores = ['Pendiente'=>'warning','Programada'=>'primary','Finalizada'=>'success','Cancelada'=>'danger'];
                            @endphp
                            <span class="badge bg-{{ $colores[$cita->estado] ?? 'secondary' }}">
                                {{ $cita->estado }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                            No hay citas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación manual --}}
@if($citas->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
            Mostrando {{ $citas->firstItem() }}–{{ $citas->lastItem() }}
            de {{ $citas->total() }} citas
        </div>
        <div class="sa-pagination">
            {{-- Anterior --}}
            @if($citas->onFirstPage())
                <span class="sa-page-btn disabled"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $citas->previousPageUrl() }}" class="sa-page-btn">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            {{-- Páginas --}}
            @php
                $start = max(1, $citas->currentPage() - 2);
                $end   = min($citas->lastPage(), $citas->currentPage() + 2);
            @endphp

            @if($start > 1)
                <a href="{{ $citas->url(1) }}" class="sa-page-btn">1</a>
                @if($start > 2)
                    <span class="sa-page-btn disabled">…</span>
                @endif
            @endif

            @for($p = $start; $p <= $end; $p++)
                <a href="{{ $citas->url($p) }}"
                   class="sa-page-btn {{ $citas->currentPage() === $p ? 'active' : '' }}">
                    {{ $p }}
                </a>
            @endfor

            @if($end < $citas->lastPage())
                @if($end < $citas->lastPage() - 1)
                    <span class="sa-page-btn disabled">…</span>
                @endif
                <a href="{{ $citas->url($citas->lastPage()) }}" class="sa-page-btn">
                    {{ $citas->lastPage() }}
                </a>
            @endif

            {{-- Siguiente --}}
            @if($citas->hasMorePages())
                <a href="{{ $citas->nextPageUrl() }}" class="sa-page-btn">
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span class="sa-page-btn disabled"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
    </div>
@endif

<style>
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
.sa-page-btn i {
    font-size: .8rem;
    line-height: 1;
}
</style>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    function buildUrl() {
        const centro = $('#filtroCentro').val();
        const estado = $('#filtroEstado').val();
        const params = new URLSearchParams();
        if (centro) params.set('centro_id', centro);
        if (estado) params.set('estado', estado);
        // Volver a página 1 al cambiar filtros
        params.set('page', '1');
        window.location.href = `{{ route('superadmin.usuarios.citas') }}?${params.toString()}`;
    }

    $('#filtroCentro, #filtroEstado').on('change', buildUrl);

    $('#btnLimpiar').on('click', function () {
        window.location.href = `{{ route('superadmin.usuarios.citas') }}`;
    });
});
</script>
@endsection