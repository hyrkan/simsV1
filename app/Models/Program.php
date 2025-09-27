<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $guarded = [];

    /**
     * Get the department that owns the program.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the curricula for the program.
     */
    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }
}
