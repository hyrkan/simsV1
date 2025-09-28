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
        Schema::table('admissions', function (Blueprint $table) {
            // Add control number field - unique identifier for each admission
            $table->string('control_number', 20)->unique()->nullable()->after('id');
            
            // Add index for better performance
            $table->index('control_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropIndex(['control_number']);
            $table->dropColumn('control_number');
        });
    }
};
