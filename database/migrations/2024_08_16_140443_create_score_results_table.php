<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('test_id')->references('id')->on('testmedbyte_results');
            $table->foreignId('nivel_riesgo_id')->references('id')->on('nivel_riesgos');
            $table->string('score_total',50);
            $table->string('score_ansiedad',50);
            $table->string('score_estres',50);
            $table->text('alerta_medbyte');
            $table->date('fecha_aplicacion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_results');
    }
};
