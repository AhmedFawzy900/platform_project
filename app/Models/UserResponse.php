<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResponse extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'question_id', 'choice_id', 'written_response',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Relationship with Choice
    public function choice()
    {
        return $this->belongsTo(Choice::class);
    }
}
