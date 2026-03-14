@extends('emails.layout')
@section('contenido')
    <h2>✅ Tu ticket fue tomado</h2>

    <div class="alert alert-success">
        Un administrador ha tomado tu ticket y está siendo atendido.
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Ticket #</span>
            <span class="info-value">{{ $ticket->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Asunto</span>
            <span class="info-value">{{ $ticket->asunto }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Atendido por</span>
            <span class="info-value">{{ $ticket->admin->name }} {{ $ticket->admin->Apellidos }}</span>
        </div>
    </div>

    <div class="btn-center">
        <a href="{{ $urlTicket }}" class="btn btn-primary">Ver ticket</a>
    </div>
@endsection