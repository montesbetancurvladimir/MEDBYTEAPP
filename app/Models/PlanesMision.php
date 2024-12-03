<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanesMision extends Model
{
    use HasFactory;

    // Definir la tabla si el nombre no sigue la convención pluralizada
    protected $table = 'planes_misiones';

    // Si el modelo no usa el campo "id" como clave primaria
    protected $primaryKey = 'id';

    // Si el campo 'id' no es auto-incrementable
    public $incrementing = true;

    // La clave primaria es de tipo integer
    protected $keyType = 'int';

    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'plan_personalizado_id',
        'mision_id',
        'completado',
        'fecha_inicio',
        'fecha_fin',
        'habilitado'
    ];

    // Si los campos de marca de tiempo no se usan
    public $timestamps = true;

    // Define la relación con el modelo PlanesPersonalizado
    public function planPersonalizado()
    {
        return $this->belongsTo(PlanesPersonalizado::class, 'plan_personalizado_id');
    }

    // Define la relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define la relación con el modelo ListadoMision
    public function mision(){
        return $this->belongsTo(ListadoMision::class, 'mision_id');
    }

    // Métodos adicionales, relaciones, o lógica del modelo pueden ir aquí
}
