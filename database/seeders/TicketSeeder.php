<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cita;
use App\Models\Ticket;
use App\Models\TicketMensaje;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ticket_mensajes')->truncate();
        DB::table('ticket_archivos')->truncate();
        DB::table('tickets')->truncate();

        $admin   = User::where('admin', 1)->first();
        $medicos = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->get();

        if ($medicos->isEmpty() || !$admin) {
            $this->command->warn('⚠️  No hay médicos o admin para crear tickets.');
            return;
        }

        $ticketsData = [
            [
                'asunto'      => 'Problema con acceso al sistema de horarios',
                'descripcion' => 'No puedo modificar mis horarios de atención. Al intentar guardar aparece un error 500.',
                'prioridad'   => 'alta',
                'estado'      => 'cerrado',
                'medico_idx'  => 0,
                'admin'       => true,
                'mensajes'    => [
                    ['emisor' => 'medico', 'contenido' => 'El error ocurre cada vez que intento guardar cambios en el horario del viernes.'],
                    ['emisor' => 'admin',  'contenido' => 'Revisamos el sistema, era un problema de validación de días. Ya fue corregido.'],
                    ['emisor' => 'medico', 'contenido' => 'Perfecto, ya funciona correctamente. Muchas gracias.'],
                ],
            ],
            [
                'asunto'      => 'Solicitud de nuevo horario de atención',
                'descripcion' => 'Me gustaría extender mi horario de atención los días miércoles hasta las 20:00.',
                'prioridad'   => 'media',
                'estado'      => 'en_progreso',
                'medico_idx'  => 1,
                'admin'       => true,
                'mensajes'    => [
                    ['emisor' => 'admin',  'contenido' => 'Hola Dra. Ana, revisaremos su solicitud de extensión de horario.'],
                    ['emisor' => 'medico', 'contenido' => 'Gracias, quedo atenta a la respuesta.'],
                ],
            ],
            [
                'asunto'      => 'Error al generar informe PDF',
                'descripcion' => 'Al intentar descargar el informe en PDF de algunas citas, el archivo sale en blanco.',
                'prioridad'   => 'alta',
                'estado'      => 'abierto',
                'medico_idx'  => 2,
                'admin'       => false,
                'mensajes'    => [],
            ],
            [
                'asunto'      => 'Consulta sobre asignación de especialidades',
                'descripcion' => 'Quisiera que me asignaran también la especialidad de Neurología además de la que ya tengo.',
                'prioridad'   => 'baja',
                'estado'      => 'abierto',
                'medico_idx'  => 3,
                'admin'       => false,
                'mensajes'    => [],
            ],
        ];

        $ticketsCreados = 0;
        $mensajesCreados = 0;

        foreach ($ticketsData as $data) {
            $medico = $medicos[$data['medico_idx'] % $medicos->count()];

            // Cita relacionada opcional
            $cita = Cita::where('medico_id', $medico->id)->first();

            $ticket = Ticket::create([
                'medico_id'   => $medico->id,
                'admin_id'    => $data['admin'] ? $admin->id : null,
                'cita_id'     => $cita?->id,
                'asunto'      => $data['asunto'],
                'descripcion' => $data['descripcion'],
                'prioridad'   => $data['prioridad'],
                'estado'      => $data['estado'],
                'tomado_en'   => $data['admin'] ? now()->subDays(rand(1, 3)) : null,
            ]);

            // Mensajes del ticket
            foreach ($data['mensajes'] as $msg) {
                $emisorId = $msg['emisor'] === 'admin' ? $admin->id : $medico->id;
                TicketMensaje::create([
                    'ticket_id'  => $ticket->id,
                    'emisor_id'  => $emisorId,
                    'contenido'  => $msg['contenido'],
                    'leido'      => true,
                    'created_at' => now()->subHours(rand(1, 48)),
                    'updated_at' => now()->subHours(rand(1, 48)),
                ]);
                $mensajesCreados++;
            }

            $ticketsCreados++;
        }

        $this->command->info("✅ Tickets creados: {$ticketsCreados}");
        $this->command->info("   → Abiertos:     " . Ticket::where('estado', 'abierto')->count());
        $this->command->info("   → En progreso:  " . Ticket::where('estado', 'en_progreso')->count());
        $this->command->info("   → Cerrados:     " . Ticket::where('estado', 'cerrado')->count());
        $this->command->info("   → Mensajes:     {$mensajesCreados}");
    }
}