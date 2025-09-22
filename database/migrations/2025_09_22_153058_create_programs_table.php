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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Short code (e.g., BSCS, BSA, BAComm)
            $table->string('name'); // Full program name
            $table->text('description')->nullable(); // Optional: program details
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade'); // Points to the department offering the program
            $table->integer('duration_years'); // Standard years to finish (e.g., 4, 5)
            $table->string('degree_level'); // Undergraduate, Graduate, Diploma, Certificate
            $table->string('status')->default('Active'); // Active, Inactive, Archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
