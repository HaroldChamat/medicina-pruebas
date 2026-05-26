<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('centro_medico', function (Blueprint $table) {
            if (!Schema::hasColumn('centro_medico', 'creado_por_superadmin_id')) {
                $table->unsignedBigInteger('creado_por_superadmin_id')
                    ->nullable()
                    ->after('direccion');
            }
        });
    }
 
    public function down(): void
    {
        Schema::table('centro_medico', function (Blueprint $table) {
            if (Schema::hasColumn('centro_medico', 'creado_por_superadmin_id')) {
                $table->dropColumn('creado_por_superadmin_id');
            }
        });
    }
};