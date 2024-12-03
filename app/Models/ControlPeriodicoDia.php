<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlPeriodicoDia extends Model
{
    use HasFactory;
    protected $table = 'control_periodico_dias';
    protected $fillable = [
        'dias'
    ];
}
