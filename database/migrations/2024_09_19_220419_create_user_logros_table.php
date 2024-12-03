<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_logros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('logro_id')->references('id')->on('listado_logros');
            $table->foreignId('plan_personalizado_id')->references('id')->on('planes_personalizados');
            $table->foreignId('mision_id')->references('id')->on('listado_misiones'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logros');
    }
};
