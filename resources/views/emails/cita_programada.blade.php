@extends('emails.layout')
@section('contenido')
    <h2>📅 Cita médica programada</h2>

    <div class="alert alert-success">
        ✅ Tu cita médica ha sido confirmada y programada exitosamente.
    </div>

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
    </div>

    <div class="btn-center">
        <a href="{{ $urlCitas }}" class="btn btn-primary">Ver mis citas</a>
    </div>
@endsection