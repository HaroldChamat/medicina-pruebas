<?php

namespace App\Events;

use App\Models\Cita;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevaCitaCreada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Cita $cita;
    public int  $paraUserId;
    public string $titulo;
    public string $mensaje;
    public string $tipo;
    public string $url;

    public function __construct(Cita $cita, int $paraUserId, string $titulo, string $mensaje, string $tipo = 'info', string $url = '')
    {
        $this->cita        = $cita;
        $this->paraUserId  = $paraUserId;
        $this->titulo      = $titulo;
        $this->mensaje     = $mensaje;
        $this->tipo        = $tipo;
        $this->url         = $url;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('notificaciones.' . $this->paraUserId);
    }

    public function broadcastAs(): string
    {
        return 'nueva-notificacion';
    }

    public function broadcastWith(): array
    {
        return [
            'titulo'  => $this->titulo,
            'mensaje' => $this->mensaje,
            'tipo'    => $this->tipo,
            'url'     => $this->url,
            'hora'    => now()->format('H:i'),
        ];
    }
}