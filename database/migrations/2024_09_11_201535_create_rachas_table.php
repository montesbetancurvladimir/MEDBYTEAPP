<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rachas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->integer('racha_actual')->default(0); // Días consecutivos
            $table->date('ultima_fecha_accion')->nullable(); // Última fecha de acción
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rachas');
    }
};
