<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    public function run(): void
    {
        // Evitar duplicados si se corre más de una vez
        DB::table('cargos')->truncate();

        $cargos = [
            ['Nombre_cargo' => 'Paciente'],
            ['Nombre_cargo' => 'Medico'],
            ['Nombre_cargo' => 'Otro'],   // Admin general
        ];

        foreach ($cargos as $cargo) {
            \App\Models\Cargo::create($cargo);
        }

        $this->command->info('✅ Cargos creados: Paciente, Medico, Otro');
    }
}