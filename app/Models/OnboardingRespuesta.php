<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingRespuesta extends Model
{
    use HasFactory;
    protected $table = 'onboarding_respuestas';
    protected $fillable = [
        'onboarding_pregunta_id',
        'respuesta',
        'siguien_onboarding_pregunta_id'
    ];

}
