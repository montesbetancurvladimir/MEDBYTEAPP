<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50); // Especifica la longitud máxima si es conocido
            $table->string('nombre', 100); // Especifica la longitud máxima si es conocido
            $table->string('tokens', 100); //CANTIDAD DE TOKENS QUE DA EL PLAN A USUARIO POR SUSCRIBIRSE A EL MENSUAL
            $table->text('descripcion'); // Usa text si puede ser largo
            $table->json('caracteristicas'); // Mantén json si es un array de características
            $table->string('valor', 50); //mensual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
