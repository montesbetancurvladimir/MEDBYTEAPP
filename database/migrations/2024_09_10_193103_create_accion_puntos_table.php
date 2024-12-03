<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        Schema::create('accion_puntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->integer('puntos');
            $table->string('accion');
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('accion_puntos');
    }
};
