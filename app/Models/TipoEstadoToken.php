<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEstadoToken extends Model
{
    protected $table = 'tipo_estado_tokens';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Las marcas de tiempo 'created_at' y 'updated_at' están habilitadas por defecto
}