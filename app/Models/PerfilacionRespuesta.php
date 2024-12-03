<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilacionRespuesta extends Model
{
    use HasFactory;
    protected $table = 'perfilacion_respuestas';
    protected $fillable = [
        'perfilacion_pregunta_id',
        'respuesta',
        'siguiente_perfilacion_pregunta_id'
    ];

}
