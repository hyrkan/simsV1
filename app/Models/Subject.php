<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $guarded = [];

    /**
     * Get the department that owns the subject.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the curricula that include this subject.
     */
    public function curricula(): BelongsToMany
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_subject')
            ->withPivot([
                'year_level',
                'semester',
                'order',
                'is_required',
                'units_override'
            ])
            ->withTimestamps();
    }
}
