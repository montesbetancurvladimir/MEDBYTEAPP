<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPunto extends Model
{
    // Nombre de la tabla (si no sigues la convención de Laravel)
    protected $table = 'user_puntos';

    // Campos que pueden ser asignados masivamente
    protected $fillable = ['user_id', 'total_points'];

    /**
     * Relación con el modelo User (usuario).
     * Un registro en 'user_puntos' pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
