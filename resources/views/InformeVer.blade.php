@extends('layouts.app')
@section('content')

<div class="container mt-4">

    {{-- Encabezado --}}
    <div class="page-header d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-medical me-2"></i> Informe Médico
            </h4>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">
                Código:
                <strong>{{ $cita->codigo_cita ?? 'CIT-' . $cita->id }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('informe.edit', $cita->id) }}"
               class="btn btn-warning btn-sm rounded-pill">
                <i class="bi bi-pencil me-1"></i> Editar informe
            </a>
            <a href="{{ route('informe.pdf', $cita->id) }}"
               class="btn btn-danger btn-sm rounded-pill" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
            </a>
            <a href="/citas" class="btn btn-outline-light btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    {{-- Cards de info --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="icon-dash mx-auto mb-2 bg-primary-soft">
                    <i class="bi bi-person-badge fs-4 text-primary"></i>
                </div>
                <p class="text-muted small mb-1">Médico</p>
                <p class="fw-semibold mb-0">
                    {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}
                </p>
                @foreach($cita->medico->especialidades as $esp)
                    <span class="badge bg-light text-dark border mt-1" style="font-size: 0.7rem;">
                        {{ $esp->Nombre_especialidad }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="icon-dash mx-auto mb-2 bg-success-soft">
                    <i class="bi bi-person fs-4 text-success"></i>
                </div>
                <p class="text-muted small mb-1">Paciente</p>
                <p class="fw-semibold mb-0">
                    {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                </p>
                <small class="text-muted">{{ $cita->paciente->Rut }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="icon-dash mx-auto mb-2 bg-warning-soft">
                    <i class="bi bi-calendar-event fs-4 text-warning"></i>
                </div>
                <p class="text-muted small mb-1">Fecha y hora</p>
                <p class="fw-semibold mb-0">
                    {{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="icon-dash mx-auto mb-2 bg-danger-soft">
                    <i class="bi bi-qr-code fs-4 text-danger"></i>
                </div>
                <p class="text-muted small mb-1">Código de cita</p>
                <p class="fw-bold mb-0" style="letter-spacing: 1px; color: #0d3b6e;">
                    {{ $cita->codigo_cita ?? 'CIT-' . $cita->id }}
                </p>
            </div>
        </div>
    </div>

    {{-- Contenido del informe --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header text-white fw-semibold"
                     style="background-color: #0d3b6e;">
                    <i class="bi bi-virus me-2"></i> Diagnóstico
                </div>
                <div class="card-body p-4">
                    <p class="mb-0" style="white-space: pre-wrap; line-height: 1.7;">
                        {{ $cita->enfermedad->descripcion ?? 'No registrado' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header text-white fw-semibold"
                     style="background-color: #0d3b6e;">
                    <i class="bi bi-capsule me-2"></i> Tratamiento
                </div>
                <div class="card-body p-4">
                    <p class="mb-0" style="white-space: pre-wrap; line-height: 1.7;">
                        {{ $cita->tratamiento->descripcion ?? 'No registrado' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .icon-dash {
        width: 52px; height: 52px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .bg-primary-soft { background-color: #e8f0fb; }
    .bg-success-soft { background-color: #e6f9f0; }
    .bg-warning-soft { background-color: #fff8e1; }
    .bg-danger-soft  { background-color: #fdecea; }
</style>

@endsection