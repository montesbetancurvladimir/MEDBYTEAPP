<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('testmedbyte_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->text('respuestas');
            $table->text('tipo');
            $table->date('fecha_aplicacion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testmedbyte_results');
    }
};
