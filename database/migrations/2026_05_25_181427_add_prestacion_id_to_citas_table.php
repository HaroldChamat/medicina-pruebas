<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('prestacion_id')
                ->nullable()
                ->after('paciente_id')
                ->constrained('prestaciones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['prestacion_id']);
            $table->dropColumn('prestacion_id');
        });
    }
};