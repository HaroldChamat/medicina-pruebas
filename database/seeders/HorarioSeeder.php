<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Horario;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('horarios')->truncate();

        $medicos = User::whereHas('cargo', function ($q) {
            $q->where('Nombre_cargo', 'Medico');
        })->get();

        $configuraciones = [
            [
                'hora_inicio'     => '08:00',
                'hora_fin'        => '17:00',
                'almuerzo_inicio' => '13:00',
                'almuerzo_fin'    => '14:00',
                'dias_semana'     => ['lunes','martes','miercoles','jueves','viernes'],
            ],
            [
                'hora_inicio'     => '09:00',
                'hora_fin'        => '18:00',
                'almuerzo_inicio' => '13:30',
                'almuerzo_fin'    => '14:30',
                'dias_semana'     => ['lunes','martes','jueves','viernes'],
            ],
            [
                'hora_inicio'     => '08:30',
                'hora_fin'        => '16:30',
                'almuerzo_inicio' => '12:30',
                'almuerzo_fin'    => '13:30',
                'dias_semana'     => ['lunes','miercoles','viernes'],
            ],
            [
                'hora_inicio'     => '10:00',
                'hora_fin'        => '19:00',
                'almuerzo_inicio' => '14:00',
                'almuerzo_fin'    => '15:00',
                'dias_semana'     => ['martes','miercoles','jueves'],
            ],
        ];

        foreach ($medicos as $index => $medico) {
            $config = $configuraciones[$index % count($configuraciones)];

            Horario::create([
                'medico_id'       => $medico->id,
                'hora_inicio'     => $config['hora_inicio'],
                'hora_fin'        => $config['hora_fin'],
                'almuerzo_inicio' => $config['almuerzo_inicio'],
                'almuerzo_fin'    => $config['almuerzo_fin'],
                'dias_semana'     => $config['dias_semana'],
            ]);

            $dias = implode(', ', $config['dias_semana']);
            $this->command->info(
                "   → Dr. {$medico->name} {$medico->Apellidos}" .
                " ({$config['hora_inicio']} - {$config['hora_fin']}," .
                " días: {$dias})"
            );
        }

        $this->command->info('✅ Horarios creados para ' . $medicos->count() . ' médicos');
    }
}