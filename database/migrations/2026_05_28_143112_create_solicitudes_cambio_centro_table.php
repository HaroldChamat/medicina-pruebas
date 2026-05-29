<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_cambio_centro', function (Blueprint $table) {
            $table->id();

            // Quien hace la solicitud (paciente, médico o admin)
            $table->foreignId('solicitante_id')
                ->constrained('users')
                ->onDelete('cascade');

            // El paciente cuyo centro se quiere cambiar
            $table->foreignId('paciente_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Centro actual del paciente al momento de la solicitud
            $table->unsignedBigInteger('centro_actual_id')->nullable();
            $table->foreign('centro_actual_id')
                ->references('id')->on('centro_medico')
                ->nullOnDelete();

            // Centro al que se quiere cambiar
            $table->unsignedBigInteger('centro_solicitado_id');
            $table->foreign('centro_solicitado_id')
                ->references('id')->on('centro_medico')
                ->onDelete('cascade');

            $table->string('asunto', 200);
            $table->text('motivo');

            // Solo médicos y admins tienen prioridad
            $table->enum('prioridad', ['alta', 'media', 'baja'])->nullable();

            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->text('nota_superadmin')->nullable();

            // Superadmin que gestionó la solicitud
            $table->unsignedBigInteger('gestionado_por')->nullable();
            $table->foreign('gestionado_por')
                ->references('id')->on('superadmins')
                ->nullOnDelete();

            $table->timestamp('gestionado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_cambio_centro');
    }
};