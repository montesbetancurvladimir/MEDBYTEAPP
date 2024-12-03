<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'text', 'next_question_id'];

    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function nextQuestion(){
        return $this->belongsTo(Question::class, 'next_question_id');
    }

}
