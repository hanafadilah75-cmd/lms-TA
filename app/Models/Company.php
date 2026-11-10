<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'type',
    ];

    /**
     * Check if company is platform startup.
     */
    public function isStartup(): bool
    {
        return $this->type === 'startup';
    }

    /**
     * Check if company is client company.
     */
    public function isClient(): bool
    {
        return $this->type === 'client';
    }

    /**
     * Get the employees/users of the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * The courses assigned to this client company (many-to-many).
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'company_course');
    }
}
