<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListadoSubmision extends Model
{
    use HasFactory;
    // Definir la tabla si el nombre no sigue la convención pluralizada
    protected $table = 'listado_submisiones';
    // Si el modelo no usa el campo "id" como clave primaria
    protected $primaryKey = 'id';
    // Si el campo 'id' no es auto-incrementable
    public $incrementing = true;
    // La clave primaria es de tipo integer
    protected $keyType = 'int';
    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'mision_id',
        'codigo',
        'nombre',
        'descripcion',
        'puntos',
    ];
    // Si los campos de marca de tiempo no se usan
    public $timestamps = true;
    // Define la relación con el modelo ListadoReto
    public function reto()
    {
        return $this->belongsTo(ListadoMision::class, 'mision_id');
    }

    // Métodos adicionales, relaciones, o lógica del modelo pueden ir aquí
}
