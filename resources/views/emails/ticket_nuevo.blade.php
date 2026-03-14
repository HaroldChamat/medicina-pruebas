@extends('emails.layout')
@section('contenido')
    <h2>🎫 Nuevo ticket de soporte</h2>

    <div class="alert alert-warning">
        Un médico ha creado un ticket que requiere atención.
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Ticket #</span>
            <span class="info-value">{{ $ticket->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Médico</span>
            <span class="info-value">Dr. {{ $ticket->medico->name }} {{ $ticket->medico->Apellidos }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Asunto</span>
            <span class="info-value">{{ $ticket->asunto }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Prioridad</span>
            <span class="info-value">
                <span class="badge badge-{{ $ticket->prioridad }}">{{ ucfirst($ticket->prioridad) }}</span>
            </span>
        </div>
    </div>

    <div class="message-box">{{ $ticket->descripcion }}</div>

    <div class="btn-center">
        <a href="{{ $urlTicket }}" class="btn btn-primary">Ver y tomar ticket</a>
    </div>
@endsection