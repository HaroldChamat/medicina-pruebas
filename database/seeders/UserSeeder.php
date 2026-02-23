<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cargo;
use App\Models\Especialidad;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();

        $cargoAdmin    = Cargo::where('Nombre_cargo', 'Otro')->first();
        $cargoMedico   = Cargo::where('Nombre_cargo', 'Medico')->first();
        $cargoPaciente = Cargo::where('Nombre_cargo', 'Paciente')->first();

        $esp_general     = Especialidad::where('Nombre_especialidad', 'Medicina General')->first();
        $esp_cardiologia = Especialidad::where('Nombre_especialidad', 'Cardiología')->first();
        $esp_pediatria   = Especialidad::where('Nombre_especialidad', 'Pediatría')->first();
        $esp_trauma      = Especialidad::where('Nombre_especialidad', 'Traumatología')->first();

        // ─── ADMINISTRADOR ─────────────────────────────────────────────
        User::create([
            'name'          => 'Admin',
            'Apellidos'     => 'Sistema',
            'email'         => 'admin@clinica.cl',
            'Rut'           => 11111111,
            'telefono'      => 912345678,
            'id_cargo'      => $cargoAdmin->id,
            'admin'         => 1,
            'especialidad_id' => null,
        ]);

        // ─── MÉDICOS ───────────────────────────────────────────────────
        $medicos = [
            [
                'name'           => 'Carlos',
                'Apellidos'      => 'Ramírez',
                'email'          => 'carlos.ramirez@clinica.cl',
                'Rut'            => 12345678,
                'telefono'       => 921111111,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_general->id,
            ],
            [
                'name'           => 'Ana',
                'Apellidos'      => 'López',
                'email'          => 'ana.lopez@clinica.cl',
                'Rut'            => 12345679,
                'telefono'       => 922222222,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_cardiologia->id,
            ],
            [
                'name'           => 'Roberto',
                'Apellidos'      => 'Fuentes',
                'email'          => 'roberto.fuentes@clinica.cl',
                'Rut'            => 12345680,
                'telefono'       => 923333333,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_pediatria->id,
            ],
            [
                'name'           => 'Valentina',
                'Apellidos'      => 'Torres',
                'email'          => 'valentina.torres@clinica.cl',
                'Rut'            => 12345681,
                'telefono'       => 924444444,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_trauma->id,
            ],
        ];

        foreach ($medicos as $medico) {
            User::create($medico);
        }

        // ─── PACIENTES ─────────────────────────────────────────────────
        $pacientes = [
            [
                'name'      => 'Juan',
                'Apellidos' => 'Pérez',
                'email'     => 'juan.perez@mail.com',
                'Rut'       => 20000001,
                'telefono'  => 956111111,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
            ],
            [
                'name'      => 'María',
                'Apellidos' => 'González',
                'email'     => 'maria.gonzalez@mail.com',
                'Rut'       => 20000002,
                'telefono'  => 956222222,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
            ],
            [
                'name'      => 'Luis',
                'Apellidos' => 'Herrera',
                'email'     => 'luis.herrera@mail.com',
                'Rut'       => 20000003,
                'telefono'  => 956333333,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
            ],
            [
                'name'      => 'Sofía',
                'Apellidos' => 'Muñoz',
                'email'     => 'sofia.munoz@mail.com',
                'Rut'       => 20000004,
                'telefono'  => 956444444,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
            ],
            [
                'name'      => 'Diego',
                'Apellidos' => 'Castro',
                'email'     => 'diego.castro@mail.com',
                'Rut'       => 20000005,
                'telefono'  => 956555555,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
            ],
            [
                'name'      => 'Camila',
                'Apellidos' => 'Vargas',
                'email'     => 'camila.vargas@mail.com',
                'Rut'       => 20000006,
                'telefono'  => 956666666,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
            ],
        ];

        foreach ($pacientes as $paciente) {
            User::create($paciente);
        }

        $this->command->info('✅ Usuarios creados:');
        $this->command->info('   → 1 Administrador  (RUT: 11111111)');
        $this->command->info('   → 4 Médicos        (RUTs: 12345678 al 12345681)');
        $this->command->info('   → 6 Pacientes      (RUTs: 20000001 al 20000006)');
    }
}