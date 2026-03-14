@extends('emails.layout')
@section('contenido')
    <h2>📅 Nueva cita médica asignada</h2>

    <p style="color:#555; margin-bottom:16px;">
        Se ha registrado una nueva cita médica en el sistema.
    </p>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Código</span>
            <span class="info-value">{{ $cita->codigo_cita ?? 'CIT-'.$cita->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Médico</span>
            <span class="info-value">Dr. {{ $cita->medico->name }} {{ $cita->medico->Apellidos }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Paciente</span>
            <span class="info-value">{{ $cita->paciente->name }} {{ $cita->paciente->Apellidos }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha y hora</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado</span>
            <span class="info-value">⏳ Pendiente</span>
        </div>
    </div>

    <div class="alert alert-info">
        Como médico asignado, puedes programar o cancelar esta cita desde el sistema.
    </div>

    <div class="btn-center">
        <a href="{{ $urlProgramar }}" class="btn btn-success">✅ Programar cita</a>
        <a href="{{ $urlCancelar }}" class="btn btn-danger">❌ Cancelar cita</a>
    </div>
@endsection