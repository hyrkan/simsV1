<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'exam_schedule' => 'datetime',
        'elementary_year' => 'integer',
        'junior_high_year' => 'integer',
        'senior_high_year' => 'integer',
        'age' => 'integer',
    ];

    /**
     * Get the program that the admission belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the major that the admission belongs to.
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get the academic term that the admission belongs to.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the full name of the student.
     */
    public function getFullNameAttribute(): string
    {
        $fullName = $this->given_name;
        
        if ($this->middle_name) {
            $fullName .= ' ' . $this->middle_name;
        }
        
        $fullName .= ' ' . $this->surname;
        
        return $fullName;
    }

    /**
     * Get the full birth address.
     */
    public function getBirthAddressAttribute(): string
    {
        $address = '';
        
        if ($this->birth_sitio) {
            $address .= $this->birth_sitio . ', ';
        }
        
        $address .= $this->birth_barangay . ', ' . $this->birth_city . ', ' . $this->birth_province;
        
        return $address;
    }

    /**
     * Get the full home address.
     */
    public function getHomeAddressAttribute(): string
    {
        $address = '';
        
        if ($this->home_sitio) {
            $address .= $this->home_sitio . ', ';
        }
        
        $address .= $this->home_barangay . ', ' . $this->home_city . ', ' . $this->home_province;
        
        return $address;
    }

    /**
     * Scope to filter by application status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('application_status', $status);
    }

    /**
     * Scope to filter by student status.
     */
    public function scopeByStudentStatus($query, $status)
    {
        return $query->where('student_status', $status);
    }

    /**
     * Scope to filter by program.
     */
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }
}
