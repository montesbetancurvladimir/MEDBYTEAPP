<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'descripcion',
        'indicativo',
        'next_question_id'
    ];

    public function nextQuestion(){
        return $this->belongsTo(Question::class, 'next_question_id');
    }
}
