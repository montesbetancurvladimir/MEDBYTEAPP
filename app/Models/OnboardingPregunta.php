<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingPregunta extends Model
{
    use HasFactory;
    protected $table = 'onboarding_preguntas';
    protected $fillable = [
        'texto',
        'tipo_pregunta'
    ];

}
