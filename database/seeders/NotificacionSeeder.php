<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Notificacion;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notificaciones')->truncate();

        $admin   = User::where('admin', 1)->first();
        $medicos = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->get();
        $pacientes = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))->get();

        $notificaciones = [];

        // Notificaciones para el admin
        if ($admin) {
            $notificaciones[] = [
                'user_id' => $admin->id,
                'titulo'  => 'Sistema iniciado',
                'mensaje' => 'El sistema de gestión médica está listo.',
                'tipo'    => 'success',
                'url'     => '/citas',
                'leida'   => true,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ];
            $notificaciones[] = [
                'user_id' => $admin->id,
                'titulo'  => 'Nuevos usuarios registrados',
                'mensaje' => 'Se han registrado 6 pacientes y 4 médicos en el sistema.',
                'tipo'    => 'info',
                'url'     => '/Especialidad',
                'leida'   => false,
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ];
        }

        // Notificaciones para médicos
        foreach ($medicos as $medico) {
            $notificaciones[] = [
                'user_id' => $medico->id,
                'titulo'  => 'Bienvenido al sistema',
                'mensaje' => "Dr. {$medico->name}, tu horario ha sido configurado correctamente.",
                'tipo'    => 'success',
                'url'     => '/Horario',
                'leida'   => true,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ];
            $notificaciones[] = [
                'user_id' => $medico->id,
                'titulo'  => 'Citas pendientes',
                'mensaje' => 'Tienes citas programadas para los próximos días.',
                'tipo'    => 'info',
                'url'     => '/citas',
                'leida'   => false,
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ];
        }

        // Notificaciones para pacientes
        foreach ($pacientes as $paciente) {
            $notificaciones[] = [
                'user_id' => $paciente->id,
                'titulo'  => 'Bienvenido',
                'mensaje' => "Hola {$paciente->name}, bienvenido al sistema de gestión médica.",
                'tipo'    => 'info',
                'url'     => '/citas',
                'leida'   => true,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ];
        }

        foreach ($notificaciones as $n) {
            Notificacion::create($n);
        }

        $this->command->info('✅ Notificaciones creadas: ' . count($notificaciones));
    }
}