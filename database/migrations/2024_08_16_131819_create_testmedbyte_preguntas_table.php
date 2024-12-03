<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testmedbyte_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('texto');
            $table->string('variable');
            $table->foreignId('tipo_pregunta_id')->references('id')->on('testmedbyte_tipo_preguntas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testmedbyte_preguntas');
    }
};
