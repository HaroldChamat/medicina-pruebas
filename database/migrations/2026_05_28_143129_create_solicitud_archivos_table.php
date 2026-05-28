<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_archivos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_id')
                ->constrained('solicitudes_cambio_centro')
                ->onDelete('cascade');

            $table->foreignId('emisor_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('mime_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_archivos');
    }
};