<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Major extends Model
{
    protected $guarded = [];

    /**
     * Get the program that owns the major.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
