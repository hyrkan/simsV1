<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicTerms = [
            [
                'school_year' => '2024-2025',
                'semester' => '1st Sem',
                'start_date' => '2024-08-15',
                'end_date' => '2024-12-20',
                'status' => 'active',
            ],
            [
                'school_year' => '2024-2025',
                'semester' => '2nd Sem',
                'start_date' => '2025-01-15',
                'end_date' => '2025-05-30',
                'status' => 'inactive',
            ],
            [
                'school_year' => '2024-2025',
                'semester' => 'Summer',
                'start_date' => '2025-06-01',
                'end_date' => '2025-07-31',
                'status' => 'inactive',
            ],
            [
                'school_year' => '2023-2024',
                'semester' => '1st Sem',
                'start_date' => '2023-08-15',
                'end_date' => '2023-12-20',
                'status' => 'archived',
            ],
            [
                'school_year' => '2023-2024',
                'semester' => '2nd Sem',
                'start_date' => '2024-01-15',
                'end_date' => '2024-05-30',
                'status' => 'archived',
            ],
            [
                'school_year' => '2023-2024',
                'semester' => 'Summer',
                'start_date' => '2024-06-01',
                'end_date' => '2024-07-31',
                'status' => 'archived',
            ],
        ];

        foreach ($academicTerms as $term) {
            AcademicTerm::create($term);
        }
    }
}
