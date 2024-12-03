<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'type'];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function openResponses()
    {
        return $this->hasMany(OpenResponse::class);
    }
}
