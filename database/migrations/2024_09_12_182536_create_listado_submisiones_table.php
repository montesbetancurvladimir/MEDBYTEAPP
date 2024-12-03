<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listado_submisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mision_id')->references('id')->on('listado_misiones');
            $table->string('codigo'); 
            $table->string('nombre');
            $table->string('descripcion');
            $table->integer('nivel');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listado_submisiones');
    }
};
