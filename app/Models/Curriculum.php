<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
