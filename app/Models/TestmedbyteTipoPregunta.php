<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestmedbyteTipoPregunta extends Model
{
    use HasFactory;

    protected $table = 'testmedbyte_tipo_preguntas';

    protected $fillable = [
        'nombre',
        'descripción',
    ];
}
