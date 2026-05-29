@extends('layouts.app')
@section('content')

<div class="container mt-4" style="max-width: 780px;">

    {{-- Encabezado --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text me-2"></i>
                Solicitud #{{ $solicitud->id }}
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">
                {{ $solicitud->asunto }}
            </p>
        </div>
        <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    {{-- Estado + prioridad --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        @php
            $estadoColors = ['pendiente'=>'warning text-dark','aceptada'=>'success','rechazada'=>'danger'];
            $estadoIcons  = ['pendiente'=>'⏳','aceptada'=>'✅','rechazada'=>'❌'];
            $prioColors   = ['alta'=>'danger','media'=>'warning text-dark','baja'=>'success'];
        @endphp
        <span class="badge bg-{{ $estadoColors[$solicitud->estado] ?? 'secondary' }} rounded-pill px-3 py-2 fs-6">
            {{ $estadoIcons[$solicitud->estado] ?? '' }} {{ ucfirst($solicitud->estado) }}
        </span>
        @if($solicitud->prioridad)
            <span class="badge bg-{{ $prioColors[$solicitud->prioridad] ?? 'secondary' }} rounded-pill px-3 py-2 fs-6">
                {{ ucfirst($solicitud->prioridad) }} prioridad
            </span>
        @endif
    </div>

    {{-- Cards de información --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body">
                    <p class="text-muted small fw-bold text-uppercase mb-3"
                       style="letter-spacing:.07em;">Partes</p>
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="text-muted small d-block">
                                <i class="bi bi-person me-1"></i> Solicitante
                            </span>
                            <span class="fw-semibold">
                                {{ $solicitud->solicitante->name ?? '—' }}
                                {{ $solicitud->solicitante->Apellidos ?? '' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">
                                <i class="bi bi-person-heart me-1"></i> Paciente
                            </span>
                            <span class="fw-semibold">
                                {{ $solicitud->paciente->name ?? '—' }}
                                {{ $solicitud->paciente->Apellidos ?? '' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">
                                <i class="bi bi-calendar me-1"></i> Fecha de envío
                            </span>
                            <span class="fw-semibold">
                                {{ $solicitud->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-body">
                    <p class="text-muted small fw-bold text-uppercase mb-3"
                       style="letter-spacing:.07em;">Centros médicos</p>
                    <div class="d-flex flex-column gap-3">
                        <div class="rounded-3 p-2 px-3"
                             style="background:#fee2e2; border:1px solid #fca5a5;">
                            <span class="text-muted small d-block">Centro actual</span>
                            <span class="fw-bold" style="color:#b91c1c;">
                                {{ $solicitud->centroActual->nombre ?? 'Sin centro asignado' }}
                            </span>
                        </div>
                        <div class="text-center text-muted">
                            <i class="bi bi-arrow-down fs-5"></i>
                        </div>
                        <div class="rounded-3 p-2 px-3"
                             style="background:#dcfce7; border:1px solid #86efac;">
                            <span class="text-muted small d-block">Centro solicitado</span>
                            <span class="fw-bold" style="color:#15803d;">
                                {{ $solicitud->centroSolicitado->nombre ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Motivo --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-header text-white fw-semibold" style="background-color:#0d3b6e; border-radius:12px 12px 0 0;">
            <i class="bi bi-chat-text me-2"></i> Motivo
        </div>
        <div class="card-body">
            <p class="mb-0" style="white-space:pre-wrap; line-height:1.7;">{{ $solicitud->motivo }}</p>
        </div>
    </div>

    {{-- Archivos adjuntos --}}
    @if($solicitud->archivos->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-header text-white fw-semibold" style="background-color:#0d3b6e; border-radius:12px 12px 0 0;">
            <i class="bi bi-paperclip me-2"></i>
            Documentos adjuntos ({{ $solicitud->archivos->count() }})
        </div>
        <div class="card-body d-flex flex-column gap-2">
            @foreach($solicitud->archivos as $archivo)
                <a href="{{ asset('storage/' . $archivo->ruta) }}"
                   target="_blank"
                   download="{{ $archivo->nombre_original }}"
                   class="d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none text-dark"
                   style="border:1px solid #e9ecef; background:#f8f9fa; transition:.15s;"
                   onmouseover="this.style.borderColor='#0d3b6e';this.style.background='#eff6ff';"
                   onmouseout="this.style.borderColor='#e9ecef';this.style.background='#f8f9fa';">
                    <i class="bi bi-file-earmark-fill text-primary fs-5 flex-shrink-0"></i>
                    <span class="flex-grow-1 text-truncate small fw-semibold">{{ $archivo->nombre_original }}</span>
                    <span class="text-muted small flex-shrink-0">Descargar ↓</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Respuesta del superadmin --}}
    @if($solicitud->nota_superadmin)
    <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #6366f1 !important;">
        <div class="card-body" style="background:#f5f3ff; border-radius:12px;">
            <p class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing:.07em; color:#6366f1 !important;">
                <i class="bi bi-shield-check me-1"></i> Nota del superadministrador
            </p>
            <p class="mb-0" style="white-space:pre-wrap; color:#4c1d95;">{{ $solicitud->nota_superadmin }}</p>
            @if($solicitud->gestionado_en)
                <p class="text-muted small mt-2 mb-0">
                    Gestionada el {{ $solicitud->gestionado_en->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection