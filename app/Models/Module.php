<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'video_url',
        'order_number',
        'total_pages',
    ];

    /**
     * Get the course that owns this module.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get user progress records for this module.
     */
    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }
}
