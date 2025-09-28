<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        
        // Seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);
        
        // Seed academic terms and exams
        $this->call(AcademicTermSeeder::class);
        $this->call(ExamSeeder::class);
    }
}
