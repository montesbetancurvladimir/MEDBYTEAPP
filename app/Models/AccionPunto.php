<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionPunto extends Model
{
    protected $table = 'accion_puntos';
    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'user_id',
        'puntos',
        'action'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
