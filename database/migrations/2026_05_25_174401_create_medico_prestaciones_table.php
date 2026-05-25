<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico_prestaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_medico')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('id_prestacion')
                ->constrained('prestaciones')
                ->onDelete('cascade');

            // Cantidad de horas asignadas a esta prestación en el día
            $table->unsignedSmallInteger('cantidad_horas');

            // Minutos por paciente (tiempo de atención por cita)
            $table->unsignedSmallInteger('hora_atencion');

            // Horario de esta prestación
            $table->time('hora_entrada');
            $table->time('hora_salida');

            // Cuántas personas pueden tomar hora online
            $table->unsignedSmallInteger('cant_online')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_prestaciones');
    }
};