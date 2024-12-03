<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users'); 
            $table->string('titulo');
            $table->text('descripcion');
            $table->foreignId('emocion')->references('id')->on('emociones'); // Una opción o lista de emociones
            $table->string('tags')->nullable(); // Tags o etiquetas opcionales
            $table->string('ubicacion')->nullable(); // Ubicación opcional
            $table->json('adjuntos')->nullable(); // Almacenar imágenes, videos, audios, etc.
            $table->boolean('privacidad')->default(true); // Privada por defecto
            $table->timestamp('fecha_nota')->nullable(); // Fecha específica de la nota
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
