<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // Especifica la tabla asociada al modelo
    protected $table = 'planes';

    // Permite la asignación masiva para estos campos
    protected $fillable = [
        'tipo',
        'nombre',
        'descripcion',
        'caracteristicas',
        'valor',
        'tokens'
    ];

    // Si las características se guardan como JSON, puedes añadir un cast para obtenerlo como un array automáticamente
    protected $casts = [
        'caracteristicas' => 'array',
    ];
}
