<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlPeriodicoPregunta extends Model
{
    use HasFactory;
    protected $table = 'control_periodico_preguntas';
    protected $fillable = [
        'texto',
        'tipo_pregunta'
    ];

}
