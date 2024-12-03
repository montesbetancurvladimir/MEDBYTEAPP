<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfilacion extends Model
{
    use HasFactory;
    protected $table = 'perfilaciones';
    protected $fillable = [
        'user_id',
        'respuestas'
    ];

}
