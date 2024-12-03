<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listado_misiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->references('id')->on('planes_categorias');
            $table->foreignId('periodo_id')->references('id')->on('tipo_periodos');
            $table->foreignId('nivel_riesgo_id')->references('id')->on('nivel_riesgos');
            $table->string('icono');
            $table->string('codigo');
            $table->string('nombre');
            $table->string('descripcion');
            $table->integer('puntos');
            $table->timestamps();
            
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('listado_misiones');
    }
};
