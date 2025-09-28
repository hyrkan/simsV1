<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicTerm extends Model
{
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope for active terms
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for archived terms
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    // Get formatted term name
    public function getFormattedNameAttribute()
    {
        return $this->school_year . ', ' . $this->semester;
    }

    /**
     * Get the exams for the academic term.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
