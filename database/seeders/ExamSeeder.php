<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\AcademicTerm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get academic terms to associate with exams
        $activeTerms = AcademicTerm::where('status', 'active')->get();
        $inactiveTerms = AcademicTerm::where('status', 'inactive')->get();
        $archivedTerms = AcademicTerm::where('status', 'archived')->get();
        
        $allTerms = $activeTerms->concat($inactiveTerms)->concat($archivedTerms);
        
        if ($allTerms->isEmpty()) {
            // If no terms exist, create some sample data
            $sampleTerm = AcademicTerm::create([
                'school_year' => '2024-2025',
                'semester' => '1st Sem',
                'start_date' => '2024-08-15',
                'end_date' => '2024-12-15',
                'status' => 'active'
            ]);
            $allTerms = collect([$sampleTerm]);
        }
        
        // MWF dates in September 2025
        $mwfDates = [
            '2025-09-01', // Monday
            '2025-09-03', // Wednesday
            '2025-09-05', // Friday
            '2025-09-08', // Monday
            '2025-09-10', // Wednesday
            '2025-09-12', // Friday
            '2025-09-15', // Monday
            '2025-09-17', // Wednesday
            '2025-09-19', // Friday
            '2025-09-22', // Monday
            '2025-09-24', // Wednesday
            '2025-09-26', // Friday
            '2025-09-29', // Monday
        ];
        
        // 2-hour time slots from 8 AM to 6 PM
        $timeSlots = [
            ['start' => '08:00:00', 'end' => '10:00:00'],
            ['start' => '10:00:00', 'end' => '12:00:00'],
            ['start' => '12:00:00', 'end' => '14:00:00'],
            ['start' => '14:00:00', 'end' => '16:00:00'],
            ['start' => '16:00:00', 'end' => '18:00:00'],
        ];
        
        $examTypes = [
            'Mathematics Entrance Exam',
            'English Proficiency Test',
            'Science Aptitude Test',
            'General Knowledge Assessment',
            'Computer Literacy Exam',
            'Filipino Proficiency Test',
            'Social Studies Exam',
            'Critical Thinking Assessment'
        ];
        
        $locations = [
            'Room 101, Academic Building',
            'Room 102, Academic Building',
            'Room 103, Academic Building',
            'Laboratory 1, Science Building',
            'Laboratory 2, Science Building',
            'Computer Lab 1',
            'Computer Lab 2',
            'Auditorium, Main Building'
        ];
        
        $examCounter = 0;
        
        foreach ($mwfDates as $date) {
            foreach ($timeSlots as $slot) {
                $examType = $examTypes[$examCounter % count($examTypes)];
                $location = $locations[$examCounter % count($locations)];
                $term = $allTerms[$examCounter % $allTerms->count()];
                
                Exam::create([
                    'academic_term_id' => $term->id,
                    'title' => $examType,
                    'description' => "Comprehensive {$examType} for admission assessment.",
                    'exam_date' => $date,
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                    'location' => $location,
                    'max_capacity' => 30,
                    'status' => 'scheduled'
                ]);
                
                $examCounter++;
            }
        }
    }
}
