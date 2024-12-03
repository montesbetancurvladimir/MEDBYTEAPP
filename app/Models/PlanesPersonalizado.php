<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanesPersonalizado extends Model
{
    use HasFactory;
    // Definir la tabla si el nombre no sigue la convención pluralizada
    protected $table = 'planes_personalizados';
    // Si el modelo no usa el campo "id" como clave primaria
    protected $primaryKey = 'id';
    // Si el campo 'id' no es auto-incrementable
    public $incrementing = true;
    // La clave primaria es de tipo integer
    protected $keyType = 'int';
    // Los campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'fecha_inicio',
        'fecha_fin',
        'contador_dias',
        'json_misiones_dimensiones'
    ];
    // Si los campos de marca de tiempo no se usan
    public $timestamps = true;
    // Define la relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
