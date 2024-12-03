<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Racha extends Model
{
    protected $fillable = ['user_id', 'racha_actual', 'ultima_fecha_accion'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
