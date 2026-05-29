<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CentroMedico;
use App\Models\User;

class AsignarCentroMedicoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Crear o encontrar el centro médico principal ──────────────────
        $centro = CentroMedico::firstOrCreate(
            ['nombre' => 'Centro Médico Principal'],
            [
                'nombre'    => 'Centro Médico Principal',
                'direccion' => 'Av. Principal 1234, Santiago',
            ]
        );

        $this->command->info("Centro médico: [{$centro->id}] {$centro->nombre}");

        // ── 2. Asignar TODOS los usuarios sin centro al centro principal ──────
        $actualizados = User::whereNull('centro_medico_id')
            ->update(['centro_medico_id' => $centro->id]);

        $this->command->info("Usuarios asignados al centro: {$actualizados}");

        // ── 3. Mostrar resumen ────────────────────────────────────────────────
        $admins    = User::where('centro_medico_id', $centro->id)->where('admin', 1)->count();
        $medicos   = User::where('centro_medico_id', $centro->id)
                         ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->count();
        $pacientes = User::where('centro_medico_id', $centro->id)
                         ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))->count();

        $this->command->table(
            ['Tipo', 'Cantidad'],
            [
                ['Admins',    $admins],
                ['Médicos',   $medicos],
                ['Pacientes', $pacientes],
            ]
        );

        $this->command->info('✅ Seeder completado correctamente.');
    }
}