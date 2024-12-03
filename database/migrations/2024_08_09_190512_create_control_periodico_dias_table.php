<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_periodico_dias', function (Blueprint $table) {
            $table->id();
            $table->string('dias');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_periodico_dias');
    }
};
