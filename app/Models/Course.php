<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'category_id', 'title', 'description', 'cover_image', 'price',
    ];

    protected $dates = ['deleted_at'];

    // Define the relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
