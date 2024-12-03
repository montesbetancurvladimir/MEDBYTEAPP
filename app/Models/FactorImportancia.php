<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactorImportancia extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        //Estres
        'scoreStress',
        'nivelRiesgoStress',
        'P1_WOE_Stress',
        'P3_WOE_Stress',
        'P4_WOE_Stress',
        'P12_WOE_Stress',
        'P16_WOE_Stress',
        'P20_WOE_Stress',
        'P25_WOE_Stress',
        'P32_WOE_Stress',
        'P36_WOE_Stress',
        'P26_WOE_Stress',
        //Ansiedad
        'scoreAnsiedad',
        'nivelRiesgoAnsiedad',
        'P2_WOE_Anxiety',
        'P4_WOE_Anxiety',
        'P7_WOE_Anxiety',
        'P9_WOE_Anxiety',
        'P11_WOE_Anxiety',
        'P12_WOE_Anxiety',
        'P16_WOE_Anxiety',
        'P18_WOE_Anxiety',
        'P19_WOE_Anxiety',
        'P29_WOE_Anxiety',
        'P31_WOE_Anxiety',
        'P32_WOE_Anxiety',
        //Mensajes de Alerta
        'mensaje_1',
        'mensaje_2',
        'mensaje_3',
        'mensaje_4',
        'mensaje_5',
        //Score total
        'scoreTotal',
        'nivelRiesgoTotal',
        'P1_Total_WOE',
        'P2_Total_WOE',
        'P3_Total_WOE',
        'P4_Total_WOE',
        'P7_Total_WOE',
        'P9_Total_WOE',
        'P11_Total_WOE',
        'P12_Total_WOE',
        'P16_Total_WOE',
        'P18_Total_WOE',
        'P19_Total_WOE',
        'P20_Total_WOE',
        'P25_Total_WOE',
        'P29_Total_WOE',
        'P31_Total_WOE',
        'P32_Total_WOE',
        'P36_Total_WOE',
        'P26_Total_WOE'
    ];
}
