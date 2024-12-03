<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Asegúrate de incluir esta referencia para hacer inserciones

return new class extends Migration
{
    public function up(): void{
        Schema::create('tipo_periodos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo');
            $table->timestamps();
        });

        // Inserción de los registros iniciales
        DB::table('tipo_periodos')->insert([
            ['nombre' => 'Mensual', 'codigo' => 'mensual', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Semanal', 'codigo' => 'semanal', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Diario', 'codigo' => 'diario', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void{
        Schema::dropIfExists('tipo_periodos');
    }
};
