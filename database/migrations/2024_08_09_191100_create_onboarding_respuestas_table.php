<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_pregunta_id')->nullable()->constrained('onboarding_preguntas');
            $table->string('respuesta');
            $table->foreignId('siguiente_onboarding_pregunta_id')->nullable()->constrained('onboarding_preguntas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_respuestas');
    }
};
