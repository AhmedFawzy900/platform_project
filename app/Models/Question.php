<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'quiz_id', 'question', 'type',
    ];

    // Relationship with Quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Relationship with Choices
    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    // Relationship with User Responses
    public function responses()
    {
        return $this->hasMany(UserResponse::class);
    }
}
