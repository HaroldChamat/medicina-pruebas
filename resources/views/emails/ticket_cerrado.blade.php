@extends('emails.layout')
@section('contenido')
    <h2>🔒 Ticket cerrado</h2>

    <div class="alert alert-warning">
        Tu ticket ha sido cerrado por el administrador.
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
            <span class="info-label">Cerrado por</span>
            <span class="info-value">{{ $ticket->admin->name }} {{ $ticket->admin->Apellidos }}</span>
        </div>
    </div>

    <div class="btn-center">
        <a href="{{ $urlTicket }}" class="btn btn-primary">Ver ticket</a>
    </div>
@endsection