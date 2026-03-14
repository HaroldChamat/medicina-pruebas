@extends('emails.layout')
@section('contenido')
    <h2>📋 Informe médico {{ $actualizado ? 'actualizado' : 'generado' }}</h2>

    <div class="alert alert-success">
        {{ $actualizado ? 'Tu informe médico ha sido actualizado.' : 'Tu informe médico ha sido generado y está disponible.' }}
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Código cita</span>
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
            <span class="info-label">Fecha cita</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($cita->Fecha_y_hora)->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="btn-center">
        <a href="{{ $urlVer }}" class="btn btn-primary">Ver informe</a>
    </div>
@endsection