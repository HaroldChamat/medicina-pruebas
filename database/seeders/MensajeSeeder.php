<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cita;
use App\Models\Mensaje;
use Carbon\Carbon;

class MensajeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mensajes')->truncate();

        // Solo citas Programadas o Finalizadas recientes (con chat activo)
        $citas = Cita::whereIn('estado', ['Programada', 'Finalizada'])
            ->where(function ($q) {
                $q->where('estado', 'Programada')
                  ->orWhere(function ($q2) {
                      $q2->where('estado', 'Finalizada')
                         ->where('Fecha_y_hora', '>=', Carbon::now()->subDays(2));
                  });
            })
            ->with(['medico', 'paciente'])
            ->take(3)
            ->get();

        if ($citas->isEmpty()) {
            $this->command->warn('⚠️  No hay citas Programadas o Finalizadas recientes para mensajes de chat.');
            return;
        }

        $conversaciones = [
            [
                ['emisor' => 'paciente', 'contenido' => 'Buenos días doctor, tengo una consulta sobre mi próxima cita.'],
                ['emisor' => 'medico',   'contenido' => 'Buenos días, con gusto le ayudo. ¿En qué le puedo orientar?'],
                ['emisor' => 'paciente', 'contenido' => '¿Debo venir en ayunas para la consulta?'],
                ['emisor' => 'medico',   'contenido' => 'Sí, por favor venga con 8 horas de ayuno para los exámenes de rutina.'],
                ['emisor' => 'paciente', 'contenido' => 'Entendido, muchas gracias doctor.'],
            ],
            [
                ['emisor' => 'paciente', 'contenido' => 'Doctor, ¿puedo tomar el medicamento que me recetó con el estómago lleno?'],
                ['emisor' => 'medico',   'contenido' => 'Sí, de hecho es recomendable tomarlo con alimentos para evitar molestias gástricas.'],
                ['emisor' => 'paciente', 'contenido' => 'Perfecto, gracias por la aclaración.'],
            ],
            [
                ['emisor' => 'medico',   'contenido' => 'Estimado paciente, le recuerdo que su cita es mañana a las 10:00.'],
                ['emisor' => 'paciente', 'contenido' => 'Muchas gracias por el recordatorio, ahí estaré puntual.'],
                ['emisor' => 'medico',   'contenido' => 'Perfecto. Traiga sus exámenes anteriores si los tiene.'],
                ['emisor' => 'paciente', 'contenido' => 'Los tengo listos, hasta mañana doctor.'],
            ],
        ];

        $mensajesCreados = 0;

        foreach ($citas as $index => $cita) {
            $conv = $conversaciones[$index % count($conversaciones)];

            foreach ($conv as $i => $msg) {
                $esMedico  = $msg['emisor'] === 'medico';
                $emisorId  = $esMedico ? $cita->medico_id   : $cita->paciente_id;
                $receptorId= $esMedico ? $cita->paciente_id : $cita->medico_id;

                Mensaje::create([
                    'cita_id'    => $cita->id,
                    'emisor_id'  => $emisorId,
                    'receptor_id'=> $receptorId,
                    'contenido'  => $msg['contenido'],
                    'leido'      => $i < count($conv) - 1, // último no leído
                    'created_at' => now()->subMinutes((count($conv) - $i) * 10),
                    'updated_at' => now()->subMinutes((count($conv) - $i) * 10),
                ]);

                $mensajesCreados++;
            }
        }

        $this->command->info("✅ Mensajes de chat creados: {$mensajesCreados}");
        $this->command->info("   → En {$citas->count()} conversaciones activas");
    }
}