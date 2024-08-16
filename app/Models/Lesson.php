<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'course_id', 'title', 'description', 'order',
    ];

    protected $dates = ['deleted_at'];

    // Define the relationship with Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Define the relationship with Materials
    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}
