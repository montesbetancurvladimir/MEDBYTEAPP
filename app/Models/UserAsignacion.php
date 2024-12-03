<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAsignacion extends Model
{
    use HasFactory;

    // Definimos el nombre de la tabla
    protected $table = 'users_asignaciones';

    // Los campos que se pueden llenar de forma masiva
    protected $fillable = [
        'user_id',
        'retos_mensuales',
        'retos_semanales',
        'ultima_asignacion_mensual',
        'ultima_asignacion_semanal',
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
