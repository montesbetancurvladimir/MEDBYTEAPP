<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListadoMision extends Model
{
    use HasFactory;
    // Definir la tabla si el nombre no sigue la convención pluralizada
    protected $table = 'listado_misiones';
    // Si el modelo no usa el campo "id" como clave primaria
    protected $primaryKey = 'id';
    // Si el campo 'id' no es auto-incrementable
    public $incrementing = true;
    // La clave primaria es de tipo integer
    protected $keyType = 'int';
    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'categoria_id',
        'codigo',
        'nivel_riesgo_id',
        'icono',
        'nombre',
        'descripcion',
        'puntos',
    ];

    // Si los campos de marca de tiempo no se usan
    public $timestamps = true;
    // Define la relación con el modelo PlanesCategoria
    public function categoria(){
        return $this->belongsTo(PlanesCategoria::class, 'categoria_id');
    }
}
