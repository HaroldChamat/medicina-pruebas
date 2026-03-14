<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use App\Models\Cita;
use App\Models\Ticket;

class CorreoHelper
{
    // ── Cita creada (a médico y admins) ──────────────────────────────────
    public static function citaCreada(Cita $cita): void
    {
        $cita->load(['medico', 'paciente']);

        $datos = [
            'cita'         => $cita,
            'urlProgramar' => url('/citas'),
            'urlCancelar'  => url('/citas'),
        ];

        // Al médico
        self::enviar(
            $cita->medico->email,
            '📅 Nueva cita asignada — ' . ($cita->codigo_cita ?? 'CIT-'.$cita->id),
            'emails.cita_creada',
            $datos
        );

        // A todos los admins
        $admins = NotificacionHelper::getAdmins();
        foreach ($admins as $admin) {
            self::enviar(
                $admin->email,
                '📅 Nueva cita registrada — ' . ($cita->codigo_cita ?? 'CIT-'.$cita->id),
                'emails.cita_creada',
                $datos
            );
        }
    }

    // ── Cita programada (a paciente y médico) ─────────────────────────────
    public static function citaProgramada(Cita $cita): void
    {
        $cita->load(['medico', 'paciente']);
        $datos = ['cita' => $cita, 'urlCitas' => url('/citas')];

        self::enviar($cita->paciente->email, '✅ Cita programada — ' . ($cita->codigo_cita ?? 'CIT-'.$cita->id), 'emails.cita_programada', $datos);
        self::enviar($cita->medico->email,   '✅ Cita programada — ' . ($cita->codigo_cita ?? 'CIT-'.$cita->id), 'emails.cita_programada', $datos);
    }

    // ── Cita cancelada (al paciente) ──────────────────────────────────────
    public static function citaCancelada(Cita $cita): void
    {
        $cita->load(['medico', 'paciente']);
        $datos = ['cita' => $cita, 'urlCitas' => url('/citas')];

        self::enviar($cita->paciente->email, '❌ Cita cancelada — ' . ($cita->codigo_cita ?? 'CIT-'.$cita->id), 'emails.cita_cancelada', $datos);
    }

    // ── Informe generado/actualizado (a paciente y médico) ────────────────
    public static function informeGenerado(Cita $cita, bool $actualizado = false): void
    {
        $cita->load(['medico', 'paciente']);
        $datos = [
            'cita'       => $cita,
            'actualizado'=> $actualizado,
            'urlVer'     => url('/Informe/' . $cita->id . '/ver'),
        ];

        $asunto = $actualizado
            ? '📋 Informe actualizado — ' . ($cita->codigo_cita ?? 'CIT-'.$cita->id)
            : '📋 Informe generado — '    . ($cita->codigo_cita ?? 'CIT-'.$cita->id);

        self::enviar($cita->paciente->email, $asunto, 'emails.informe_generado', $datos);
        self::enviar($cita->medico->email,   $asunto, 'emails.informe_generado', $datos);
    }

    // ── Nuevo ticket (a todos los admins) ─────────────────────────────────
    public static function ticketNuevo(Ticket $ticket): void
    {
        $ticket->load('medico');
        $datos = ['ticket' => $ticket, 'urlTicket' => url('/tickets/' . $ticket->id)];

        foreach (NotificacionHelper::getAdmins() as $admin) {
            self::enviar($admin->email, '🎫 Nuevo ticket #' . $ticket->id . ' — ' . $ticket->asunto, 'emails.ticket_nuevo', $datos);
        }
    }

    // ── Ticket tomado (al médico) ─────────────────────────────────────────
    public static function ticketTomado(Ticket $ticket): void
    {
        $ticket->load(['medico', 'admin']);
        $datos = ['ticket' => $ticket, 'urlTicket' => url('/tickets/' . $ticket->id)];

        self::enviar($ticket->medico->email, '✅ Tu ticket fue tomado — ' . $ticket->asunto, 'emails.ticket_tomado', $datos);
    }

    // ── Ticket cerrado (al médico) ────────────────────────────────────────
    public static function ticketCerrado(Ticket $ticket): void
    {
        $ticket->load(['medico', 'admin']);
        $datos = ['ticket' => $ticket, 'urlTicket' => url('/tickets/' . $ticket->id)];

        self::enviar($ticket->medico->email, '🔒 Ticket cerrado — ' . $ticket->asunto, 'emails.ticket_cerrado', $datos);
    }

    // ── Nuevo mensaje en chat ─────────────────────────────────────────────
    public static function nuevoMensajeChat(\App\Models\Mensaje $mensaje): void
    {
        $mensaje->load(['emisor', 'receptor', 'cita']);
        $datos = [
            'emisor'      => $mensaje->emisor->name . ' ' . $mensaje->emisor->Apellidos,
            'contenido'   => $mensaje->contenido,
            'referencia'  => $mensaje->cita->codigo_cita ?? 'CIT-'.$mensaje->cita_id,
            'esTicket'    => false,
            'urlVer'      => url('/chat/' . $mensaje->cita_id),
        ];

        self::enviar(
            $mensaje->receptor->email,
            '💬 Nuevo mensaje de ' . $datos['emisor'],
            'emails.nuevo_mensaje',
            $datos
        );
    }

    // ── Nuevo mensaje en ticket ───────────────────────────────────────────
    public static function nuevoMensajeTicket(\App\Models\TicketMensaje $mensaje, \App\Models\User $receptor): void
    {
        $mensaje->load(['emisor', 'ticket']);
        $datos = [
            'emisor'     => $mensaje->emisor->name . ' ' . $mensaje->emisor->Apellidos,
            'contenido'  => $mensaje->contenido,
            'referencia' => '#' . $mensaje->ticket->id . ' — ' . $mensaje->ticket->asunto,
            'esTicket'   => true,
            'urlVer'     => url('/tickets/' . $mensaje->ticket_id),
        ];

        self::enviar(
            $receptor->email,
            '💬 Nuevo mensaje en ticket #' . $mensaje->ticket_id,
            'emails.nuevo_mensaje',
            $datos
        );
    }

    // ── Envío genérico ────────────────────────────────────────────────────
    private static function enviar(string $destino, string $asunto, string $vista, array $datos): void
    {
        try {
            Mail::send($vista, $datos, function ($m) use ($destino, $asunto) {
                $m->to($destino)->subject($asunto);
            });
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo: ' . $e->getMessage());
        }
    }
}