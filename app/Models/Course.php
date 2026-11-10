<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tutor_id',
        'title',
        'description',
        'thumbnail',
        'level',
    ];

    /**
     * Get the tutor for this course.
     */
    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id')->withTrashed();
    }

    /**
     * Get modules for this course.
     */
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order_number');
    }

    /**
     * Get quizzes for this course.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * The client companies that are assigned to this course (many-to-many).
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_course');
    }
}
