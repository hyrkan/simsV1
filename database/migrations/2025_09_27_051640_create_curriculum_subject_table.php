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
        Schema::create('curriculum_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('subject_id');
            $table->tinyInteger('year_level')->default(1); // 1-5
            $table->string('semester', 10)->default('1st'); // 1st, 2nd, Summer, Trimester
            $table->tinyInteger('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->decimal('units_override', 4, 2)->nullable(); // optional
            $table->timestamps();   

            $table->foreign('curriculum_id')->references('id')->on('curricula')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->unique(['curriculum_id', 'subject_id']);
            $table->index(['curriculum_id', 'year_level', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_subject');
    }
};
