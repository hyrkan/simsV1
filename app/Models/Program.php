<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
