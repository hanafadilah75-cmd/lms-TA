<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'answer',
        'ai_score',
        'ai_feedback',
        'final_score',
    ];

    protected $casts = [
        'ai_score' => 'float',
        'final_score' => 'float',
    ];

    /**
     * Get the user that owns this answer.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the question associated with this answer.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the essay review for this answer (if applicable).
     */
    public function essayReview()
    {
        return $this->hasOne(EssayReview::class);
    }

    /**
     * Get the score adjustment logs for this answer.
     */
    public function scoreAdjustmentLogs()
    {
        return $this->hasMany(ScoreAdjustmentLog::class)->latest();
    }
}
