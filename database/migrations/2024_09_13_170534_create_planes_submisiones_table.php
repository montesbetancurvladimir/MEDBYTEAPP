<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void{
        Schema::create('planes_submisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_mision_id')->references('id')->on('planes_misiones'); 
            $table->foreignId('submision_id')->references('id')->on('listado_submisiones'); 
            $table->boolean('completado')->default(false);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('planes_submisiones');
    }
};
