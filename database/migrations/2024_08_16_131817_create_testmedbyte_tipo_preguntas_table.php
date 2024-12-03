<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testmedbyte_tipo_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripción');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testmedbyte_tipo_preguntas');
    }
};
