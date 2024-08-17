<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'question_id', 'choice_text', 'is_correct',
    ];

    // Relationship with Question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
