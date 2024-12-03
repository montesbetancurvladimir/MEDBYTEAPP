<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_estado_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion');
            $table->timestamps();
        });

        // Agregar registros a la tabla
        DB::table('tipo_estado_tokens')->insert([
            ['nombre' => 'Pendiente', 'descripcion' => 'Token pendiente por ser usado.', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Gastado', 'descripcion' => 'Token usado.', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_estado_tokens');
    }
};
