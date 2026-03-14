<?php

namespace App\Helpers;

use App\Models\Notificacion;
use App\Models\User;
use App\Events\NuevaCitaCreada;
use App\Models\Cita;

class NotificacionHelper
{
    /**
     * Crea notificación en BD y la emite por Pusher
     */
    public static function enviar(?Cita $cita = null, int $userId = 0, string $titulo = '', string $mensaje = '', string $tipo = 'info', string $url = '')
    {
        // Guardar en BD
        Notificacion::create([
            'user_id' => $userId,
            'titulo'  => $titulo,
            'mensaje' => $mensaje,
            'tipo'    => $tipo,
            'url'     => $url,
            'leida'   => false,
        ]);

        // Broadcast en tiempo real solo si hay cita válida
        if ($cita && $cita->id) {
            broadcast(new NuevaCitaCreada($cita, $userId, $titulo, $mensaje, $tipo, $url))->toOthers();
        } else {
            // Broadcast directo sin cita
            broadcast(new \App\Events\NuevaCitaCreada(
                new Cita(['id' => 0]),
                $userId, $titulo, $mensaje, $tipo, $url
            ))->toOthers();
        }
    }

    /**
     * Obtiene admins para notificarles
     */
    public static function getAdmins()
    {
        return User::where('admin', 1)->get();
    }
}