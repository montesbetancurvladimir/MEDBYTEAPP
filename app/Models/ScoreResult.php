<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreResult extends Model
{
    use HasFactory;

    protected $table = 'score_results';

    protected $fillable = [
        'user_id',
        'test_id',
        'nivel_riesgo_id',
        'score_total',
        'score_ansiedad',
        'score_estres',
        'alerta_medbyte',
        'fecha_aplicacion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nivelRiesgo()
    {
        return $this->belongsTo(NivelRiesgo::class, 'nivel_riesgo_id');
    }
}
