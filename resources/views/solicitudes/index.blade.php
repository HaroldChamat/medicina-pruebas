@extends('layouts.app')
@section('content')

<div class="container mt-4">

    {{-- Encabezado --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-building-gear me-2"></i> Mis Solicitudes de Cambio de Centro
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">
                Historial de todas tus solicitudes enviadas.
            </p>
        </div>
        <a href="{{ route('solicitudes.create') }}" class="btn btn-light rounded-pill fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Nueva solicitud
        </a>
    </div>

    @if($solicitudes->isEmpty())
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-building-slash fs-1 d-block mb-3"></i>
                <p class="fw-semibold mb-1">No tienes solicitudes aún</p>
                <small>Cuando crees una solicitud de cambio de centro, aparecerá aquí.</small>
                <br>
                <a href="{{ route('solicitudes.create') }}"
                   class="btn btn-outline-primary btn-sm rounded-pill mt-3">
                    <i class="bi bi-plus-circle me-1"></i> Crear primera solicitud
                </a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm" style="border-radius:14px; overflow:hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #0d3b6e; color: white;">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            @if(session('cargo') !== 'Paciente')
                                <th>Paciente</th>
                            @endif
                            <th>Asunto</th>
                            <th>Centro solicitado</th>
                            <th>Estado</th>
                            @if(session('cargo') !== 'Paciente')
                                <th>Prioridad</th>
                            @endif
                            <th>Fecha</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $s)
                        <tr>
                            <td class="px-4 text-muted small">{{ $s->id }}</td>

                            @if(session('cargo') !== 'Paciente')
                                <td class="fw-semibold">
                                    {{ $s->paciente->name ?? '—' }}
                                    {{ $s->paciente->Apellidos ?? '' }}
                                </td>
                            @endif

                            <td style="max-width:220px;">
                                <span class="d-block text-truncate" title="{{ $s->asunto }}">
                                    {{ $s->asunto }}
                                </span>
                            </td>

                            <td class="small">{{ $s->centroSolicitado->nombre ?? '—' }}</td>

                            <td>
                                @php
                                    $colors = [
                                        'pendiente' => 'warning text-dark',
                                        'aceptada'  => 'success',
                                        'rechazada' => 'danger',
                                    ];
                                    $icons = [
                                        'pendiente' => '⏳',
                                        'aceptada'  => '✅',
                                        'rechazada' => '❌',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $colors[$s->estado] ?? 'secondary' }} rounded-pill">
                                    {{ $icons[$s->estado] ?? '' }} {{ ucfirst($s->estado) }}
                                </span>
                            </td>

                            @if(session('cargo') !== 'Paciente')
                                <td>
                                    @if($s->prioridad)
                                        @php
                                            $pColors = ['alta'=>'danger','media'=>'warning text-dark','baja'=>'success'];
                                        @endphp
                                        <span class="badge bg-{{ $pColors[$s->prioridad] ?? 'secondary' }} rounded-pill">
                                            {{ ucfirst($s->prioridad) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            @endif

                            <td class="small text-muted">
                                {{ $s->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="text-center">
                                <a href="{{ route('solicitudes.show', $s) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="bi bi-eye me-1"></i> Ver
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($solicitudes->hasPages())
                <div class="d-flex align-items-center justify-content-between px-4 py-3"
                     style="border-top:1px solid #e9ecef; background:#f8f9fa;">
                    <div class="text-muted small">
                        Mostrando {{ $solicitudes->firstItem() }}–{{ $solicitudes->lastItem() }}
                        de {{ $solicitudes->total() }} solicitudes
                    </div>
                    {{ $solicitudes->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection