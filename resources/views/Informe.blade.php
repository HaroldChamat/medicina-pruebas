@extends('layouts.app')
@section('content')

<div class="container mt-4">

    {{-- Encabezado --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-medical me-2"></i> Crear Informe Médico
            </h4>
            <p class="text-muted small mb-0">Completa el diagnóstico y tratamiento de la cita.</p>
        </div>
        <a href="/citas" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver a citas
        </a>
    </div>

    {{-- Info de la cita --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="icon-dash mx-auto mb-2 bg-primary-soft">
                    <i class="bi bi-person-badge fs-4 text-primary"></i>
                </div>
                <p class="text-muted small mb-1">Médico</p>
                <p class="fw-semibold mb-0">
                    {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="icon-dash mx-auto mb-2 bg-success-soft">
                    <i class="bi bi-person fs-4 text-success"></i>
                </div>
                <p class="text-muted small mb-1">Paciente</p>
                <p class="fw-semibold mb-0">
                    {{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}
                </p>
            </div>
        </div>
        <div class="col-md-4">
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
    </div>

    {{-- Formulario --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white fw-bold" style="background-color: #0d3b6e;">
            <i class="bi bi-pencil-square me-2"></i> Datos del informe
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('informe.store', $cita->id) }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-virus me-1 text-danger"></i> Enfermedad diagnosticada
                    </label>
                    <textarea name="enfermedad"
                              class="form-control @error('enfermedad') is-invalid @enderror"
                              rows="4"
                              placeholder="Describe el diagnóstico del paciente..."
                              required>{{ old('enfermedad', optional($cita->enfermedad)->descripcion) }}</textarea>
                    @error('enfermedad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-capsule me-1 text-primary"></i> Tratamiento indicado
                    </label>
                    <textarea name="tratamiento"
                              class="form-control @error('tratamiento') is-invalid @enderror"
                              rows="4"
                              placeholder="Describe el tratamiento indicado para el paciente..."
                              required>{{ old('tratamiento', optional($cita->tratamiento)->descripcion) }}</textarea>
                    @error('tratamiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/citas" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-save me-1"></i> Guardar informe
                    </button>
                </div>
            </form>
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
</style>

@endsection