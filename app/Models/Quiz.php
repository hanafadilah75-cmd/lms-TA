<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'type', // multiple_choice, essay
        'passing_grade',
        'duration',
    ];

    /**
     * Get the course that owns this quiz.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get questions for this quiz.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
