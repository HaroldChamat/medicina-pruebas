<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Horario;
use App\Models\Cargo;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('horarios')->truncate();

        $medicos = User::whereHas('cargo', function ($q) {
            $q->where('Nombre_cargo', 'Medico');
        })->get();

        // Configuraciones de horario variadas para hacer pruebas más realistas
        $configuraciones = [
            [
                'hora_inicio'      => '08:00',
                'hora_fin'         => '17:00',
                'almuerzo_inicio'  => '13:00',
                'almuerzo_fin'     => '14:00',
                'hora_atencion'    => 30,   // minutos por cita
            ],
            [
                'hora_inicio'      => '09:00',
                'hora_fin'         => '18:00',
                'almuerzo_inicio'  => '13:30',
                'almuerzo_fin'     => '14:30',
                'hora_atencion'    => 45,
            ],
            [
                'hora_inicio'      => '08:30',
                'hora_fin'         => '16:30',
                'almuerzo_inicio'  => '12:30',
                'almuerzo_fin'     => '13:30',
                'hora_atencion'    => 20,
            ],
            [
                'hora_inicio'      => '10:00',
                'hora_fin'         => '19:00',
                'almuerzo_inicio'  => '14:00',
                'almuerzo_fin'     => '15:00',
                'hora_atencion'    => 60,
            ],
        ];

        foreach ($medicos as $index => $medico) {
            $config = $configuraciones[$index % count($configuraciones)];

            Horario::create(array_merge($config, [
                'medico_id' => $medico->id,
            ]));

            $this->command->info(
                "   → Horario asignado a Dr. {$medico->name} {$medico->Apellidos}" .
                " ({$config['hora_inicio']} - {$config['hora_fin']}, {$config['hora_atencion']} min/cita)"
            );
        }

        $this->command->info('✅ Horarios creados para ' . $medicos->count() . ' médicos');
    }
}