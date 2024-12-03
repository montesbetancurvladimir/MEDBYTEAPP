<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('archivos_notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_id')->references('id')->on('notas')->nullable();
            $table->string('ruta_documento',400);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('archivos');
    }
};
