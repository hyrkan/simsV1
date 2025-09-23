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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->decimal('units', 3, 1)->default(0);
            $table->integer('lecture_hours')->nullable()->default(0);
            $table->integer('lab_hours')->nullable()->default(0);
            $table->string('type', 50)->default('general'); // general, major, elective, pe, nstp, others
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('status', 20)->default('active'); // active, inactive, archived
            $table->timestamps();
            
            // Add unique constraint
            $table->unique(['code', 'department_id']);
            
            // Add foreign key constraint
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
