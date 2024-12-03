<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAccion extends Model
{
    // Nombre de la tabla (si no sigues la convención de Laravel)
    protected $table = 'plan_acciones';
    // Campos que pueden ser asignados masivamente
    protected $fillable = ['plan_id', 'action', 'points'];
    
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
