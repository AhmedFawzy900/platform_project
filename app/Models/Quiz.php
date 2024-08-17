<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $fillable = [
        'lesson_id', 'course_id', 'title', 'description',
    ];

    // Relationship with Lesson or Course
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relationship with Questions
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class,'quiz_id');
    }
}
