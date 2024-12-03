<?php

// app/Models/OpenResponse.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpenResponse extends Model
{
    protected $fillable = ['user_id', 'question_id', 'response'];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
