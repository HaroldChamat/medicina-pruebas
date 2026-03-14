@extends('emails.layout')
@section('contenido')
    <h2>💬 Nuevo mensaje {{ $esTicket ? 'en ticket' : 'en chat' }}</h2>

    <div class="alert alert-info">
        Has recibido un nuevo mensaje de <strong>{{ $emisor }}</strong>.
    </div>

    @if($esTicket)
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Ticket #</span>
                <span class="info-value">{{ $referencia }}</span>
            </div>
        </div>
    @else
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Cita</span>
                <span class="info-value">{{ $referencia }}</span>
            </div>
        </div>
    @endif

    <div class="message-box">{{ $contenido }}</div>

    <div class="btn-center">
        <a href="{{ $urlVer }}" class="btn btn-primary">
            {{ $esTicket ? 'Ver ticket' : 'Ir al chat' }}
        </a>
    </div>
@endsection