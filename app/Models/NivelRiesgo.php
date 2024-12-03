<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelRiesgo extends Model
{
    use HasFactory;

    protected $table = 'nivel_riesgos';

    protected $fillable = [
        'nombre',
    ];
}
