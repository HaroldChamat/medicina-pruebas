@extends('superadmin.superadmin')

@section('page-title', 'Médicos')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:#64748b;">Médicos</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <p class="text-muted mb-0 small">
        Todos los médicos registrados en el sistema.
        <strong>{{ $medicos->count() }}</strong> en total.
    </p>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
    <div class="card-body py-3 px-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" id="buscador" class="form-control form-control-sm"
                       placeholder="🔍 Buscar por nombre, email o RUT...">
            </div>
            <div class="col-md-3">
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
                <select id="filtroEstado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ $filtroEstado === '1' ? 'selected' : '' }}>Solo activos</option>
                    <option value="0" {{ $filtroEstado === '0' ? 'selected' : '' }}>Solo inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
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
        <table class="table sa-table mb-0" id="tablaMedicos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>RUT</th>
                    <th>Especialidades</th>
                    <th>Centro</th>
                    <th>Horario</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicos as $medico)
                    <tr data-busqueda="{{ strtolower($medico->name.' '.$medico->Apellidos.' '.$medico->email.' '.$medico->Rut) }}"
                        data-activo="{{ $medico->activo }}">
                        <td class="text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;
                                     background:linear-gradient(135deg,#1a7a4a,#2ecc71);
                                     display:flex;align-items:center;justify-content:center;
                                     color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                    {{ strtoupper(substr($medico->name,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $medico->name }} {{ $medico->Apellidos }}</div>
                                    <small class="text-muted">{{ $medico->telefono ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $medico->email }}</td>
                        <td>
                            <span class="badge rounded-pill"
                                  style="background:rgba(212,160,23,.1);color:var(--sa-gold);font-size:.72rem;">
                                {{ $medico->Rut }}
                            </span>
                        </td>
                        <td>
                            @forelse($medico->especialidades as $esp)
                                <span class="badge bg-primary me-1" style="font-size:.7rem;">{{ $esp->Nombre_especialidad }}</span>
                            @empty
                                <span class="text-muted small fst-italic">Sin especialidad</span>
                            @endforelse
                        </td>
                        <td>
                            @if($medico->centroMedico)
                                <span class="badge rounded-pill"
                                      style="background:rgba(16,185,129,.12);color:#10b981;font-size:.72rem;">
                                    <i class="bi bi-building me-1"></i>{{ $medico->centroMedico->nombre }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
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
                        <td class="text-center">
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-person-badge fs-2 d-block mb-2"></i>
                            No hay médicos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {

    function filtrar() {
        const txt    = $('#buscador').val().toLowerCase();
        const activo = $('#filtroEstado').val();
        let visible  = 0;

        $('#tablaMedicos tbody tr').each(function () {
            const coincide = (!txt || $(this).data('busqueda').includes(txt))
                && (!activo || $(this).data('activo').toString() === activo);
            $(this).toggle(coincide);
            if (coincide) visible++;
        });
    }

    $('#buscador').on('keyup', filtrar);

    $('#filtroEstado').on('change', function () {
        const estado   = $(this).val();
        const centroId = $('#filtroCentro').val();
        const params   = new URLSearchParams();
        if (centroId) params.set('centro_id', centroId);
        if (estado)   params.set('activo', estado);
        window.location.href = `{{ route('superadmin.usuarios.medicos') }}?${params.toString()}`;
    });

    $('#filtroCentro').on('change', function () {
        const centroId = $(this).val();
        const estado   = $('#filtroEstado').val();
        const params   = new URLSearchParams();
        if (centroId) params.set('centro_id', centroId);
        if (estado)   params.set('activo', estado);
        window.location.href = `{{ route('superadmin.usuarios.medicos') }}?${params.toString()}`;
    });

    $('#btnLimpiar').on('click', function () {
        window.location.href = `{{ route('superadmin.usuarios.medicos') }}`;
    });
});
</script>
@endsection