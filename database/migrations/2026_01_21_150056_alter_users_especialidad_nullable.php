<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // If the column doesn't exist, create it with a foreign key to especialidads.
        if (!Schema::hasColumn('users', 'especialidad_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('especialidad_id')
                    ->nullable()
                    ->constrained('especialidads');
            });
        } else {
            // If the column exists, make it nullable (safe-change).
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('especialidad_id')->nullable()->change();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'especialidad_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['especialidad_id']);
                $table->dropColumn('especialidad_id');
            });
        }
    }
};
