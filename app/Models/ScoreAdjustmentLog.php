<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreAdjustmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_answer_id',
        'tutor_id',
        'original_ai_score',
        'adjusted_score',
        'notes',
    ];

    public function userAnswer()
    {
        return $this->belongsTo(UserAnswer::class);
    }

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}

