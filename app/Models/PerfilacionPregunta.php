<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilacionPregunta extends Model
{
    use HasFactory;
    protected $table = 'perfilacion_preguntas';
    protected $fillable = [
        'texto',
        'tipo_pregunta'
    ];

}
