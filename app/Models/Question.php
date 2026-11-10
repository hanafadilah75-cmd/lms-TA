<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quiz_id',
        'question',
        'type', // multiple_choice, essay
        'ideal_answer',
        'keywords',
    ];

    /**
     * Get the quiz that owns this question.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get options for this question.
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Get answers for this question.
     */
    public function answers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
