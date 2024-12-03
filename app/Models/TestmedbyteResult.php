<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestmedbyteResult extends Model
{
    use HasFactory;

    protected $table = 'testmedbyte_results';

    protected $fillable = [
        'user_id',
        'respuestas',
        'tipo',
        'fecha_aplicacion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
