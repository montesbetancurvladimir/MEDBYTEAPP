<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanesSubmision extends Model
{
    use HasFactory;
    // Definir la tabla si el nombre no sigue la convención pluralizada
    protected $table = 'planes_submisiones';
    // Si el modelo no usa el campo "id" como clave primaria
    protected $primaryKey = 'id';
    // Si el campo 'id' no es auto-incrementable
    public $incrementing = true;
    // La clave primaria es de tipo integer
    protected $keyType = 'int';
    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'plan_mision_id',
        'submision_id',
        'periodo_id',
        'nivel',
        'completado',
    ];
    // Si los campos de marca de tiempo no se usan
    public $timestamps = true;
    // Define la relación con el modelo PlanesReto
    public function planMision()
    {
        return $this->belongsTo(PlanesMision::class, 'plan_mision_id');
    }

}
