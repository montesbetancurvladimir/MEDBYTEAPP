<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlesPeriodico extends Model
{
    use HasFactory;
    protected $table = 'controles_periodicos';
    protected $fillable = [
        'user_id',
        'respuestas'
    ];
}
