<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionResponse extends Model
{
    protected $fillable = ['question_id', 'device_uuid', 'response_answer', 'response_text'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
