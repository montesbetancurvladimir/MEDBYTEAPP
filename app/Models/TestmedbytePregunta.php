<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestmedbytePregunta extends Model
{
    use HasFactory;

    protected $table = 'testmedbyte_preguntas';

    protected $fillable = [
        'nombre',
        'texto',
        'variable',
        'tipo_pregunta_id',
    ];

    public function tipoPregunta()
    {
        return $this->belongsTo(TestmedbyteTipoPregunta::class, 'tipo_pregunta_id');
    }
}
