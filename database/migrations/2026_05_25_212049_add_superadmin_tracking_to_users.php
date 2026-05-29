<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega creado_por_superadmin_id a users para saber
     * qué superadmin creó a cada admin de centro médico.
     * También usado para rastrear qué admin creó a cada médico/paciente.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Superadmin que creó este usuario (admin de centro)
            $table->unsignedBigInteger('creado_por_superadmin_id')
                ->nullable()
                ->after('centro_medico_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('creado_por_superadmin_id');
        });
    }
};