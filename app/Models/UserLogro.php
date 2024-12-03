<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogro extends Model
{
    use HasFactory;
    protected $table = 'user_logros';
    protected $fillable = [
        'user_id',
        'logro_id',
        'plan_personalizado_id',
        'mision_id',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
