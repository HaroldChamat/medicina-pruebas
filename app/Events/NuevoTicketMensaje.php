<?php

namespace App\Events;

use App\Models\TicketMensaje;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoTicketMensaje implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TicketMensaje $mensaje;

    public function __construct(TicketMensaje $mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('ticket.' . $this->mensaje->ticket_id);
    }

    public function broadcastAs(): string
    {
        return 'nuevo-mensaje';
    }

    public function broadcastWith(): array
    {
        return [
            'id'        => $this->mensaje->id,
            'contenido' => $this->mensaje->contenido,
            'hora'      => $this->mensaje->created_at->format('H:i'),
            'emisor'    => $this->mensaje->emisor->name . ' ' . $this->mensaje->emisor->Apellidos,
            'emisor_id' => $this->mensaje->emisor_id,
        ];
    }
}