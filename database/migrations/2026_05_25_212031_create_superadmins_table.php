<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('Apellidos');
            $table->string('email')->unique();
            $table->string('Rut', 12)->unique();
            $table->string('telefono')->nullable();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superadmins');
    }
};