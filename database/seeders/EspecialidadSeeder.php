<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidad;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('especialidads')->truncate();

        $especialidades = [
            ['Nombre_especialidad' => 'Medicina General'],
            ['Nombre_especialidad' => 'Cardiología'],
            ['Nombre_especialidad' => 'Pediatría'],
            ['Nombre_especialidad' => 'Traumatología'],
            ['Nombre_especialidad' => 'Neurología'],
            ['Nombre_especialidad' => 'Ginecología'],
            ['Nombre_especialidad' => 'Oftalmología'],
            ['Nombre_especialidad' => 'Dermatología'],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::create($especialidad);
        }

        $this->command->info('✅ Especialidades creadas: ' . count($especialidades));
    }
}