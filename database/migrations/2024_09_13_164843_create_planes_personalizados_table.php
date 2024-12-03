<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //Se crean 5 retos por semana.
        //69 - 75 retos en 3 meses
        //Se da toda la ruta - pero se habilita por fases cada semana
        Schema::create('planes_personalizados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->date('fecha_inicio')->nullable(); 
            $table->date('fecha_fin')->nullable(); 
            $table->boolean('vigente')->default(true); 
            $table->double('por_progreso')->nullable();
            $table->integer('contador_dias');
            $table->json('json_misiones_dimensiones');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes_personalizados');
    }
};
