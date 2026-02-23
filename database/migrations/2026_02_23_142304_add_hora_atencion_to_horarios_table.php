<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->unsignedInteger('hora_atencion')
                  ->after('hora_fin')
                  ->default(30);
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn('hora_atencion');
        });
    }
};