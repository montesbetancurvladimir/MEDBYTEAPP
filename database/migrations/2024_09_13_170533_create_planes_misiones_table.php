<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        Schema::create('planes_misiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_personalizado_id')->references('id')->on('planes_personalizados');
            $table->foreignId('mision_id')->references('id')->on('listado_misiones'); 
            $table->boolean('completado')->default(false); 
            $table->boolean('habilitado')->default(false);
            //Se habilitan según el tiempo de inicio del plan - no se habilitan al mismo tiempo
            $table->date('fecha_inicio')->nullable(); 
            $table->date('fecha_fin')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('planes_misiones');
    }
};
