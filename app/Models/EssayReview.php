<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EssayReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_answer_id',
        'ai_score',
        'ai_feedback',
        'tutor_score',
        'tutor_notes',
    ];

    protected $casts = [
        'ai_score' => 'float',
        'tutor_score' => 'float',
    ];

    /**
     * Get the user answer that is being reviewed.
     */
    public function userAnswer()
    {
        return $this->belongsTo(UserAnswer::class);
    }
}
