<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Orden de ejecución respetando dependencias:
     *
     * 1. CargoSeeder         → sin dependencias
     * 2. EspecialidadSeeder  → sin dependencias
     * 3. UserSeeder          → necesita cargos y especialidades
     * 4. HorarioSeeder       → necesita médicos
     * 5. CitaSeeder          → necesita médicos y pacientes
     * 6. InformeSeeder       → necesita citas finalizadas
     * 7. NotificacionSeeder  → necesita usuarios
     * 8. TicketSeeder        → necesita médicos, admin y citas
     * 9. MensajeSeeder       → necesita citas programadas/finalizadas
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

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
            NotificacionSeeder::class,
            TicketSeeder::class,
            MensajeSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

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
        $this->command->info('   Paciente 3    → RUT: 20000003-3  / pass: paciente123');
        $this->command->info('   Paciente 4    → RUT: 20000004-4  / pass: paciente123');
        $this->command->info('   Paciente 5    → RUT: 20000005-5  / pass: paciente123');
        $this->command->info('   Paciente 6    → RUT: 20000006-6  / pass: paciente123');
        $this->command->info('');
        $this->command->info('💡 Para resetear: php artisan migrate:fresh --seed');
        $this->command->info('');
    }
}