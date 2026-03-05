<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cargo;
use App\Models\Especialidad;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();

        $cargoAdmin    = Cargo::where('Nombre_cargo', 'Admin')->first();
        $cargoMedico   = Cargo::where('Nombre_cargo', 'Medico')->first();
        $cargoPaciente = Cargo::where('Nombre_cargo', 'Paciente')->first();

        $esp_general     = Especialidad::where('Nombre_especialidad', 'Medicina General')->first();
        $esp_cardiologia = Especialidad::where('Nombre_especialidad', 'Cardiología')->first();
        $esp_pediatria   = Especialidad::where('Nombre_especialidad', 'Pediatría')->first();
        $esp_trauma      = Especialidad::where('Nombre_especialidad', 'Traumatología')->first();

        // ─── ADMINISTRADOR ─────────────────────────────────────────────
        User::create([
            'name'           => 'Admin',
            'Apellidos'      => 'Sistema',
            'email'          => 'admin@clinica.cl',
            'Rut'            => '11111111-1',
            'telefono'       => 912345678,
            'id_cargo'       => $cargoAdmin->id,
            'admin'          => 1,
            'especialidad_id'=> null,
            'password'       => Hash::make('admin123'),
        ]);

        // ─── MÉDICOS ───────────────────────────────────────────────────
        $medicos = [
            [
                'name'           => 'Carlos',
                'Apellidos'      => 'Ramírez',
                'email'          => 'carlos.ramirez@clinica.cl',
                'Rut'            => '12345678-9',
                'telefono'       => 921111111,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_general->id,
                'password'       => Hash::make('medico123'),
            ],
            [
                'name'           => 'Ana',
                'Apellidos'      => 'López',
                'email'          => 'ana.lopez@clinica.cl',
                'Rut'            => '12345679-0',
                'telefono'       => 922222222,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_cardiologia->id,
                'password'       => Hash::make('medico123'),
            ],
            [
                'name'           => 'Roberto',
                'Apellidos'      => 'Fuentes',
                'email'          => 'roberto.fuentes@clinica.cl',
                'Rut'            => '12345680-1',
                'telefono'       => 923333333,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_pediatria->id,
                'password'       => Hash::make('medico123'),
            ],
            [
                'name'           => 'Valentina',
                'Apellidos'      => 'Torres',
                'email'          => 'valentina.torres@clinica.cl',
                'Rut'            => '12345681-2',
                'telefono'       => 924444444,
                'id_cargo'       => $cargoMedico->id,
                'admin'          => 0,
                'especialidad_id'=> $esp_trauma->id,
                'password'       => Hash::make('medico123'),
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
                'Rut'       => '20000001-1',
                'telefono'  => 956111111,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
                'password'  => Hash::make('paciente123'),
            ],
            [
                'name'      => 'María',
                'Apellidos' => 'González',
                'email'     => 'maria.gonzalez@mail.com',
                'Rut'       => '20000002-2',
                'telefono'  => 956222222,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
                'password'  => Hash::make('paciente123'),
            ],
            [
                'name'      => 'Luis',
                'Apellidos' => 'Herrera',
                'email'     => 'luis.herrera@mail.com',
                'Rut'       => '20000003-3',
                'telefono'  => 956333333,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
                'password'  => Hash::make('paciente123'),
            ],
            [
                'name'      => 'Sofía',
                'Apellidos' => 'Muñoz',
                'email'     => 'sofia.munoz@mail.com',
                'Rut'       => '20000004-4',
                'telefono'  => 956444444,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
                'password'  => Hash::make('paciente123'),
            ],
            [
                'name'      => 'Diego',
                'Apellidos' => 'Castro',
                'email'     => 'diego.castro@mail.com',
                'Rut'       => '20000005-5',
                'telefono'  => 956555555,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
                'password'  => Hash::make('paciente123'),
            ],
            [
                'name'      => 'Camila',
                'Apellidos' => 'Vargas',
                'email'     => 'camila.vargas@mail.com',
                'Rut'       => '20000006-6',
                'telefono'  => 956666666,
                'id_cargo'  => $cargoPaciente->id,
                'admin'     => 0,
                'password'  => Hash::make('paciente123'),
            ],
        ];

        foreach ($pacientes as $paciente) {
            User::create($paciente);
        }

        $this->command->info('✅ Usuarios creados:');
        $this->command->info('   → 1 Administrador  (RUT: 11111111-1 / pass: admin123)');
        $this->command->info('   → 4 Médicos        (RUTs: 12345678-9 al 12345681-2 / pass: medico123)');
        $this->command->info('   → 6 Pacientes      (RUTs: 20000001-1 al 20000006-6 / pass: paciente123)');
    }
}