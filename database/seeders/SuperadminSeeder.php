<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Superadmin;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        Superadmin::truncate();

        Superadmin::create([
            'name'      => 'Super',
            'Apellidos' => 'Admin',
            'email'     => 'superadmin@clinica.cl',
            'Rut'       => '99999999-9',
            'telefono'  => '+56900000000',
            'password'  => Hash::make('superadmin123'),
        ]);

        $this->command->info('✅ Superadmin creado:');
        $this->command->info('   → Email:  superadmin@clinica.cl');
        $this->command->info('   → Pass:   superadmin123');
        $this->command->info('   → URL:    /superadmin/login');
    }
}