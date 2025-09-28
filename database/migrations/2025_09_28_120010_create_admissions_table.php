<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();

            // Program & Major Selection
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
            $table->string('year_level'); // e.g., 1st year, 2nd year
            $table->enum('student_status', ['new', 'old', 'transferee']);
            $table->foreignId('academic_term_id')->constrained('academic_terms')->onDelete('cascade');
            $table->unsignedBigInteger('exam_id')->nullable();

            // Personal Information
            $table->string('surname');
            $table->string('given_name');
            $table->string('middle_name')->nullable();
            $table->string('lrn'); // Learner Reference Number
            $table->string('email');

            // Place of Birth
            $table->string('birth_sitio')->nullable();
            $table->string('birth_barangay');
            $table->string('birth_city');
            $table->string('birth_province');

            // Personal Details
            $table->date('date_of_birth');
            $table->integer('age');
            $table->enum('sex', ['Male', 'Female']);
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated']);
            $table->string('contact_number');
            $table->string('religion')->nullable();

            // Home Address
            $table->string('home_sitio')->nullable();
            $table->string('home_barangay');
            $table->string('home_city');
            $table->string('home_province');

            // Father's Information
            $table->string('father_surname')->nullable();
            $table->string('father_given_name')->nullable();
            $table->string('father_middle_name')->nullable();
            $table->string('father_occupation')->nullable();

            // Mother's Information
            $table->string('mother_surname')->nullable();
            $table->string('mother_given_name')->nullable();
            $table->string('mother_middle_name')->nullable();
            $table->string('mother_occupation')->nullable();

            // Spouse's Information
            $table->string('spouse_surname')->nullable();
            $table->string('spouse_given_name')->nullable();
            $table->string('spouse_middle_name')->nullable();
            $table->string('spouse_occupation')->nullable();

            // Guardian Information
            $table->string('guardian_surname')->nullable();
            $table->string('guardian_given_name')->nullable();
            $table->string('guardian_middle_name')->nullable();
            $table->string('guardian_occupation')->nullable();

            // Guardian Address
            $table->string('guardian_sitio')->nullable();
            $table->string('guardian_barangay')->nullable();
            $table->string('guardian_city')->nullable();
            $table->string('guardian_province')->nullable();

            // Contact Person Information
            $table->string('contact_given_name')->nullable();
            $table->string('contact_middle_initial')->nullable();
            $table->string('contact_surname')->nullable();
            $table->string('contact_person_number')->nullable();

            // Educational Background
            $table->string('elementary_school')->nullable();
            $table->year('elementary_year')->nullable();
            $table->string('junior_high_school')->nullable();
            $table->year('junior_high_year')->nullable();
            $table->string('senior_high_school')->nullable();
            $table->year('senior_high_year')->nullable();
            $table->string('track_strand')->nullable();

            // Application Status
            $table->enum('application_status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Indexes for better performance
            $table->index(['application_status']);
            $table->index(['student_status']);
            $table->index(['program_id']);
            $table->index(['major_id']);
            $table->index(['academic_term_id']);
            $table->index(['exam_id']);
            $table->index(['email']);
            $table->index(['lrn']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
