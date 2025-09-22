<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $fillable = [
        'school_year',
        'semester',
        'start_date',
        'end_date',
        'status'
    ];

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
}
