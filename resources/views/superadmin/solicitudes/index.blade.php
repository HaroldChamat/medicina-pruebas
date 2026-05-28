@extends('superadmin.superadmin')

@section('title', 'Solicitudes de Cambio de Centro')

@push('styles')
<style>
    :root {
        --sa-primary: #4f46e5;
        --sa-primary-hover: #4338ca;
        --sa-border: #e5e7eb;
        --sa-bg: #f9fafb;
        --sa-card: #ffffff;
        --sa-text: #111827;
        --sa-muted: #6b7280;
        --sa-danger: #dc2626;
        --sa-success: #16a34a;
        --sa-warning: #d97706;
    }

    .sa-wrapper { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }

    /* Topbar */
    .sa-topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .sa-topbar h1 { font-size: 1.5rem; font-weight: 800; color: var(--sa-text); margin: 0; }
    .sa-topbar p  { font-size: .875rem; color: var(--sa-muted); margin: .2rem 0 0; }

    /* Stats rápidas */
    .sa-stats {
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .sa-stat {
        background: var(--sa-card);
        border: 1px solid var(--sa-border);
        border-radius: 10px;
        padding: .75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .65rem;
        min-width: 140px;
    }

    .sa-stat-icon { font-size: 1.4rem; }
    .sa-stat-val  { font-size: 1.4rem; font-weight: 800; color: var(--sa-text); line-height: 1; }
    .sa-stat-lbl  { font-size: .75rem; color: var(--sa-muted); margin-top: .1rem; }

    /* Filtros */
    .sa-filters {
        background: var(--sa-card);
        border: 1px solid var(--sa-border);
        border-radius: 10px;
        padding: .875rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .sa-filters input,
    .sa-filters select {
        padding: .475rem .875rem;
        border: 1.5px solid var(--sa-border);
        border-radius: 7px;
        font-size: .875rem;
        color: var(--sa-text);
        background: var(--sa-bg);
        outline: none;
        transition: border-color .18s;
    }

    .sa-filters input:focus,
    .sa-filters select:focus { border-color: var(--sa-primary); }

    .sa-filters input { min-width: 220px; flex: 1; }

    .btn-filter {
        padding: .475rem 1rem;
        background: var(--sa-primary);
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-filter:hover { background: var(--sa-primary-hover); }

    .btn-filter-reset {
        padding: .475rem .9rem;
        background: #fff;
        color: var(--sa-muted);
        border: 1.5px solid var(--sa-border);
        border-radius: 7px;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
    }

    .btn-filter-reset:hover { background: var(--sa-bg); }

    /* Tabla */
    .sa-card {
        background: var(--sa-card);
        border: 1px solid var(--sa-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.07);
    }

    .sa-table-wrap { overflow-x: auto; }

    .sa-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sa-table thead tr {
        background: var(--sa-bg);
        border-bottom: 2px solid var(--sa-border);
    }

    .sa-table th {
        padding: .7rem 1rem;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--sa-muted);
        text-align: left;
        white-space: nowrap;
    }

    .sa-table td {
        padding: .875rem 1rem;
        font-size: .9rem;
        color: var(--sa-text);
        border-bottom: 1px solid var(--sa-border);
        vertical-align: middle;
    }

    .sa-table tbody tr:last-child td { border-bottom: none; }
    .sa-table tbody tr:hover { background: #f5f3ff; }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .28rem .7rem;
        border-radius: 20px;
        font-size: .74rem;
        font-weight: 700;
    }

    .badge-pendiente { background: #fef9c3; color: #713f12; }
    .badge-aceptada  { background: #dcfce7; color: #15803d; }
    .badge-rechazada { background: #fee2e2; color: #b91c1c; }
    .badge-alta      { background: #fee2e2; color: #b91c1c; }
    .badge-media     { background: #fef9c3; color: #713f12; }
    .badge-baja      { background: #dcfce7; color: #15803d; }

    .sa-asunto {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sa-date { font-size: .8rem; color: var(--sa-muted); white-space: nowrap; }

    .btn-ver {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .35rem .85rem;
        border-radius: 7px;
        border: 1.5px solid var(--sa-border);
        background: #fff;
        font-size: .8rem;
        font-weight: 600;
        color: var(--sa-text);
        text-decoration: none;
        transition: all .15s;
        white-space: nowrap;
    }

    .btn-ver:hover {
        border-color: var(--sa-primary);
        color: var(--sa-primary);
        background: #f5f3ff;
    }

    /* Fila pendiente: resaltar levemente */
    .sa-table tbody tr.row-pendiente { background: #fffbeb; }
    .sa-table tbody tr.row-pendiente:hover { background: #fef9c3; }

    /* Empty */
    .sa-empty {
        padding: 3.5rem 1rem;
        text-align: center;
        color: var(--sa-muted);
    }

    .sa-empty-icon { font-size: 3rem; margin-bottom: .75rem; }
    .sa-empty h2 { font-size: 1.1rem; font-weight: 700; color: var(--sa-text); margin: 0 0 .4rem; }

    /* Paginación */
    .sa-pagination {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--sa-border);
        background: var(--sa-bg);
    }
</style>
@endpush

@section('content')
<div class="sa-wrapper">

    <div class="sa-topbar">
        <div>
            <h1>🏥 Solicitudes de cambio de centro</h1>
            <p>Gestiona las solicitudes enviadas por pacientes, médicos y administradores.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="sa-stats">
        @php
            $total     = $solicitudes->total();
            $pendCount = $totalPendientes;
        @endphp
        <div class="sa-stat">
            <span class="sa-stat-icon">📋</span>
            <div>
                <div class="sa-stat-val">{{ $total }}</div>
                <div class="sa-stat-lbl">Total</div>
            </div>
        </div>
        <div class="sa-stat" style="border-color:#fcd34d;">
            <span class="sa-stat-icon">⏳</span>
            <div>
                <div class="sa-stat-val" style="color:#d97706;">{{ $pendCount }}</div>
                <div class="sa-stat-lbl">Pendientes</div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('superadmin.solicitudes.index') }}" class="sa-filters">
        <input type="text" name="buscar"
               placeholder="Buscar por paciente o asunto..."
               value="{{ request('buscar') }}">

        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="pendiente" @selected(request('estado') === 'pendiente')>⏳ Pendiente</option>
            <option value="aceptada"  @selected(request('estado') === 'aceptada')>✅ Aceptada</option>
            <option value="rechazada" @selected(request('estado') === 'rechazada')>❌ Rechazada</option>
        </select>

        <select name="prioridad">
            <option value="">Todas las prioridades</option>
            <option value="alta"  @selected(request('prioridad') === 'alta')>🔴 Alta</option>
            <option value="media" @selected(request('prioridad') === 'media')>🟡 Media</option>
            <option value="baja"  @selected(request('prioridad') === 'baja')>🟢 Baja</option>
        </select>

        <button type="submit" class="btn-filter">Filtrar</button>
        <a href="{{ route('superadmin.solicitudes.index') }}" class="btn-filter-reset">Limpiar</a>
    </form>

    {{-- Tabla --}}
    <div class="sa-card">
        @if($solicitudes->isEmpty())
        <div class="sa-empty">
            <div class="sa-empty-icon">🔍</div>
            <h2>No hay solicitudes</h2>
            <p>No se encontraron solicitudes con los filtros aplicados.</p>
        </div>
        @else
        <div class="sa-table-wrap">
            <table class="sa-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Paciente</th>
                        <th>Asunto</th>
                        <th>Destino</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $s)
                    <tr class="row-{{ $s->estado }}">
                        <td style="color:var(--sa-muted);font-size:.8rem;">{{ $s->id }}</td>

                        <td>
                            <div style="font-weight:600;font-size:.875rem;">{{ $s->solicitante->name ?? '—' }}</div>
                            <div style="font-size:.75rem;color:var(--sa-muted);">
                                {{ $s->solicitante->cargo->Nombre_cargo ?? '' }}
                            </div>
                        </td>

                        <td style="font-weight:600;">{{ $s->paciente->name ?? '—' }}</td>

                        <td>
                            <span class="sa-asunto" title="{{ $s->asunto }}">{{ $s->asunto }}</span>
                        </td>

                        <td style="font-size:.85rem;">{{ $s->centroSolicitado->nombre ?? '—' }}</td>

                        <td>
                            @if($s->prioridad)
                                <span class="badge badge-{{ $s->prioridad }}">{{ ucfirst($s->prioridad) }}</span>
                            @else
                                <span style="color:var(--sa-muted);font-size:.8rem;">—</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-{{ $s->estado }}">
                                {{ $s->estado_icon }} {{ ucfirst($s->estado) }}
                            </span>
                        </td>

                        <td class="sa-date">{{ $s->created_at->format('d/m/Y H:i') }}</td>

                        <td>
                            <a href="{{ route('superadmin.solicitudes.show', $s) }}" class="btn-ver">
                                Gestionar →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($solicitudes->hasPages())
        <div class="sa-pagination">
            {{ $solicitudes->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection