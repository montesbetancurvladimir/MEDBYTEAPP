<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListadoLogro extends Model
{
    use HasFactory;
    protected $table = 'listado_logros';
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'icono'
    ];
}
