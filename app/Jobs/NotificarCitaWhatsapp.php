<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Cita;
use Carbon\Carbon;

class NotificarCitaWhatsapp
{
    public function handle()
    {
        $desde = now()->addMinutes(1);
        $hasta = now()->addMinutes(2);

        $citas = Cita::with('paciente', 'medico')
            ->whereBetween('Fecha_y_hora', [$desde, $hasta])
            ->where('estado', 'Pendiente')
            ->where('whatsapp_notificado', false)
            ->get();

        foreach ($citas as $cita) {

            if (!$cita->paciente->telefono) {
                continue;
            }

            // 👉 AQUÍ va la API real de WhatsApp
            $this->enviarWhatsapp(
                $cita->paciente->telefono,
                $this->mensaje($cita)
            );

            $cita->update(['whatsapp_notificado' => true]);
        }
    }

    private function mensaje($cita)
    {
        return
        "📅 *Recordatorio de cita médica*

        Hola {$cita->paciente->name},

        Le recordamos que tiene una cita médica programada para:

        🗓 Fecha: {$cita->Fecha_y_hora}
        👨‍⚕️ Médico: {$cita->medico->name} {$cita->medico->Apellidos}

        Por favor, llegue con 10 minutos de anticipación.

        Clínica.";
    }
}

