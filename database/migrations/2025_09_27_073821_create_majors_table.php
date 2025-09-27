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
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')
                ->constrained('programs')
                ->onDelete('cascade'); // delete majors if program is deleted
            $table->string('code', 10); // e.g., "CS", "MATH", "ENG"
            $table->string('name'); // e.g., "Computer Science", "Mathematics", "English"
            $table->string('description')->nullable(); // optional: details
            $table->string('status')->default('active'); // active, inactive, draft
            $table->timestamps();

            $table->unique(['program_id', 'code']); // prevent duplicate major codes in the same program
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
