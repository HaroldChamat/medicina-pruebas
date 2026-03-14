<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->foreignId('emisor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receptor_id')->constrained('users')->onDelete('cascade');
            $table->text('contenido');
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};