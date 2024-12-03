<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'emocion',
        'tags',
        'ubicacion',
        'adjuntos',
        'privacidad',
        'fecha_nota',
    ];

    protected $casts = [
        'adjuntos' => 'array', // Para manejar múltiples archivos como JSON
        'privacidad' => 'boolean',
        'fecha_nota' => 'datetime',
    ];

    // Definir la relación con el modelo Emocion
    public function emocion(){
        return $this->belongsTo(Emocion::class, 'emocion');
    }
}
