<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('perfilacion_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfilacion_pregunta_id')
                  ->constrained('perfilacion_preguntas')
                  ->onDelete('cascade')
                  ->index('perfilacion_respuestas_perfilacion_pregunta_id_index');
            $table->string('respuesta');
            $table->foreignId('siguiente_perfilacion_pregunta_id')
                  ->constrained('perfilacion_preguntas')
                  ->onDelete('cascade')
                  ->index('perfilacion_respuestas_siguiente_perfilacion_pregunta_id_index');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfilacion_respuestas');
    }
};
