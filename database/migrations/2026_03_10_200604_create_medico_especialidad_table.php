<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico_especialidad', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medico_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('especialidad_id')->constrained('especialidads')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_especialidad');
    }
};
