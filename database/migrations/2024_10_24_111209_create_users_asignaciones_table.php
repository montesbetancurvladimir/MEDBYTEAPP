<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
    * Los cambios de retos mensuales y semanales que puede hacer el usuario, al igual que reemplazar retos 
    */
    public function up(): void
    {
        Schema::create('users_asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('retos_mensuales')->default(0);
            $table->integer('retos_semanales')->default(0);
            $table->timestamp('ultima_asignacion_mensual')->nullable();
            $table->timestamp('ultima_asignacion_semanal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_asignaciones');
    }
};
