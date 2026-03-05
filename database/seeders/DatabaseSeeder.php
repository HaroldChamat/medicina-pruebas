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
            $this->command->info('👤 Credenciales de acceso:');
            $this->command->info('   Administrador → RUT: 11111111-1  / pass: admin123');
            $this->command->info('   Médico 1      → RUT: 12345678-9  / pass: medico123');
            $this->command->info('   Médico 2      → RUT: 12345679-0  / pass: medico123');
            $this->command->info('   Médico 3      → RUT: 12345680-1  / pass: medico123');
            $this->command->info('   Médico 4      → RUT: 12345681-2  / pass: medico123');
            $this->command->info('   Paciente 1    → RUT: 20000001-1  / pass: paciente123');
            $this->command->info('   Paciente 2    → RUT: 20000002-2  / pass: paciente123');
            $this->command->info('');
    }
}