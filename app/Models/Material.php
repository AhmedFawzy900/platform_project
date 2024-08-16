<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $fillable = [
        'lesson_id', 'type', 'url',
    ];

    protected $dates = ['deleted_at'];

    // Define the relationship with Lesson
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
