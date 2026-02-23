<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Orden de ejecución respetando dependencias entre tablas:
     *
     * 1. CargoSeeder        → sin dependencias
     * 2. EspecialidadSeeder → sin dependencias
     * 3. UserSeeder         → necesita cargos y especialidades
     * 4. HorarioSeeder      → necesita usuarios (médicos)
     * 5. CitaSeeder         → necesita usuarios (médicos y pacientes)
     * 6. InformeSeeder      → necesita citas finalizadas
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');  // ← agrega esta línea

        $this->command->info('');
        $this->command->info('🏥 Iniciando seeders del Sistema de Clínica...');
        $this->command->info('─────────────────────────────────────────────');

        $this->call([
            CargoSeeder::class,
            EspecialidadSeeder::class,
            UserSeeder::class,
            HorarioSeeder::class,
            CitaSeeder::class,
            InformeSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');  // ← y esta al final
        
            $this->command->info('─────────────────────────────────────────────');
            $this->command->info('✅ Todos los seeders ejecutados correctamente');
            $this->command->info('');
            $this->command->info('👤 Credenciales de acceso (login por RUT):');
            $this->command->info('   Administrador → RUT: 11111111');
            $this->command->info('   Médico 1      → RUT: 12345678  (Medicina General)');
            $this->command->info('   Médico 2      → RUT: 12345679  (Cardiología)');
            $this->command->info('   Médico 3      → RUT: 12345680  (Pediatría)');
            $this->command->info('   Médico 4      → RUT: 12345681  (Traumatología)');
            $this->command->info('   Paciente 1    → RUT: 20000001');
            $this->command->info('   Paciente 2    → RUT: 20000002');
            $this->command->info('');
    }
}