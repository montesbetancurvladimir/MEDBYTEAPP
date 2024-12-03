<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestmedbyteRespuesta extends Model
{
    use HasFactory;

    protected $table = 'testmedbyte_respuestas';

    protected $fillable = [
        'test_pregunta_id',
        'respuesta',
    ];

    public function pregunta()
    {
        return $this->belongsTo(TestmedbytePregunta::class, 'test_pregunta_id');
    }
}
