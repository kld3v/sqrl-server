<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question_text'];

    public function questionResponses()
    {
        return $this->hasMany(QuestionResponse::class);
    }
}
