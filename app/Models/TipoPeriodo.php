<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPeriodo extends Model
{
    use HasFactory;

    // Definir la tabla si el nombre no sigue la convención pluralizada
    protected $table = 'tipo_periodos';

    // Si el modelo no usa el campo "id" como clave primaria
    protected $primaryKey = 'id';

    // Si el campo 'id' no es auto-incrementable
    public $incrementing = true;

    // La clave primaria es de tipo integer
    protected $keyType = 'int';

    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'codigo',
    ];

    // Si los campos de marca de tiempo no se usan
    public $timestamps = true;

    // Métodos adicionales, relaciones, o lógica del modelo pueden ir aquí
}
