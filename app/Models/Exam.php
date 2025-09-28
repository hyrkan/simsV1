<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'academic_term_id',
        'title',
        'description',
        'exam_date',
        'start_time',
        'end_time',
        'location',
        'max_capacity',
        'status'
    ];

    protected $casts = [
        'exam_date' => 'datetime',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the academic term that owns the exam.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the admissions for the exam.
     */
    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }
}
