<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoNota extends Model
{
    use HasFactory;
    // Nombre de la tabla asociada
    protected $table = 'archivos_notas';
    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'nota_id',
        'ruta_documento',
    ];
    // Definir la relación con el modelo Nota
    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }
}