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
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-person-heart fs-2 d-block mb-2"></i>
                            No hay pacientes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
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

@endsection

@section('scripts')
<script>
$(document).ready(function () {
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
        window.location.href = `{{ route('superadmin.usuarios.pacientes') }}?${params.toString()}`;
    });

    $('#btnLimpiar').on('click', function () {
        window.location.href = `{{ route('superadmin.usuarios.pacientes') }}`;
    });
});
</script>
@endsection