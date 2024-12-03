<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//Tabla que almacena los datos del analisis que se hace de salud mental de cada una de las preguntas que respondió el paciente
//Se usa para el gráfico de factores de importancia y barras por facor
return new class extends Migration{
    public function up(){
        Schema::create('factor_importancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            //ESTRES
            $table->float('scoreStress', 8, 2);
            $table->text('nivelRiesgoStress');
            $table->float('P1_WOE_Stress', 8, 2);
            $table->float('P3_WOE_Stress', 8, 2);
            $table->float('P4_WOE_Stress', 8, 2);
            $table->float('P12_WOE_Stress', 8, 2);
            $table->float('P16_WOE_Stress', 8, 2);
            $table->float('P20_WOE_Stress', 8, 2);
            $table->float('P25_WOE_Stress', 8, 2);
            $table->float('P32_WOE_Stress', 8, 2);
            $table->float('P36_WOE_Stress', 8, 2);
            $table->float('P26_WOE_Stress', 8, 2);
            //ANSIEDAD
            $table->float('scoreAnsiedad', 8, 2);
            $table->text('nivelRiesgoAnsiedad');
            $table->float('P2_WOE_Anxiety', 8, 2);
            $table->float('P4_WOE_Anxiety', 8, 2);
            $table->float('P7_WOE_Anxiety', 8, 2);
            $table->float('P9_WOE_Anxiety', 8, 2);
            $table->float('P11_WOE_Anxiety', 8, 2);
            $table->float('P12_WOE_Anxiety', 8, 2);
            $table->float('P16_WOE_Anxiety', 8, 2);
            $table->float('P18_WOE_Anxiety', 8, 2);
            $table->float('P19_WOE_Anxiety', 8, 2);
            $table->float('P29_WOE_Anxiety', 8, 2);
            $table->float('P31_WOE_Anxiety', 8, 2);
            $table->float('P32_WOE_Anxiety', 8, 2);
            //MENSAJES DE ALERTA
            $table->text('mensaje_1');
            $table->text('mensaje_2');
            $table->text('mensaje_3');
            $table->text('mensaje_4');
            $table->text('mensaje_5');
            //SCORE TOTAL
            $table->float('scoreTotal', 8, 2);
            $table->text('nivelRiesgoTotal');
            $table->float('P1_Total_WOE', 8, 2);
            $table->float('P2_Total_WOE', 8, 2);
            $table->float('P3_Total_WOE', 8, 2);
            $table->float('P4_Total_WOE', 8, 2);
            $table->float('P7_Total_WOE', 8, 2);
            $table->float('P9_Total_WOE', 8, 2);
            $table->float('P11_Total_WOE', 8, 2);
            $table->float('P12_Total_WOE', 8, 2);
            $table->float('P16_Total_WOE', 8, 2);
            $table->float('P18_Total_WOE', 8, 2);
            $table->float('P19_Total_WOE', 8, 2);
            $table->float('P20_Total_WOE', 8, 2);
            $table->float('P25_Total_WOE', 8, 2);
            $table->float('P29_Total_WOE', 8, 2);
            $table->float('P31_Total_WOE', 8, 2);
            $table->float('P32_Total_WOE', 8, 2);
            $table->float('P36_Total_WOE', 8, 2);
            $table->float('P26_Total_WOE', 8, 2);
            $table->timestamps();
        });
    }
    public function down(){
        Schema::dropIfExists('factor_importancias');
    }
};
