<?php

namespace App\Events;

use App\Models\Mensaje;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoMensaje implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Mensaje $mensaje;

    public function __construct(Mensaje $mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat.cita.' . $this->mensaje->cita_id);
    }

    public function broadcastAs(): string
    {
        return 'nuevo-mensaje';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->mensaje->id,
            'cita_id'    => $this->mensaje->cita_id,
            'emisor_id'  => $this->mensaje->emisor_id,
            'contenido'  => $this->mensaje->contenido,
            'hora'       => $this->mensaje->created_at->format('H:i'),
            'emisor'     => $this->mensaje->emisor->name . ' ' . $this->mensaje->emisor->Apellidos,
        ];
    }
}