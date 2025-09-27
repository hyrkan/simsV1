<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curriculum extends Model
{
    protected $guarded = [];

    /**
     * Get the program that owns the curriculum.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the subjects associated with the curriculum.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subjects')
            ->withPivot([
                'major_id',
                'year_level',
                'semester', 
                'order',
                'is_required',
                'units_override'
            ])
            ->withTimestamps()
            ->orderBy('year_level')
            ->orderBy('semester')
            ->orderBy('order');
    }
}
