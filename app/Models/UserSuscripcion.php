<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSuscripcion extends Model
{
    // Nombre de la tabla (si no sigues la convención de Laravel)
    protected $table = 'user_suscripciones';
    // Campos que pueden ser asignados masivamente
    protected $fillable = ['user_id', 'plan_id', 'start_date', 'end_date'];

    /**
     * Relación con el modelo User (usuario).
     * Una suscripción pertenece a un usuario.
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo Plan.
     * Una suscripción pertenece a un plan.
    */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
