<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'question_id', 'option_id'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}