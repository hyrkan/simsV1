<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicTermController;
use App\Models\AcademicTerm;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Academic Terms API Routes
Route::prefix('academic-terms')->group(function () {
    Route::get('/', [AcademicTermController::class, 'index']);
    Route::post('/', [AcademicTermController::class, 'store']);
    Route::put('/{academicTerm}', [AcademicTermController::class, 'update']);
    Route::delete('/{academicTerm}', [AcademicTermController::class, 'destroy']);
    Route::get('/archived', [AcademicTermController::class, 'archiveTerms']);
});

// Additional routes for status updates
Route::post('academic-terms/{academicTerm}/activate', function (AcademicTerm $academicTerm) {
    // Deactivate all other terms
    AcademicTerm::where('status', 'active')->update(['status' => 'inactive']);
    
    // Activate the selected term
    $academicTerm->update(['status' => 'active']);
    
    return response()->json([
        'success' => true,
        'message' => 'Academic term activated successfully!',
        'data' => $academicTerm
    ]);
});

Route::post('academic-terms/{academicTerm}/archive', function (AcademicTerm $academicTerm) {
    $academicTerm->update(['status' => 'archived']);
    
    return response()->json([
        'success' => true,
        'message' => 'Academic term archived successfully!',
        'data' => $academicTerm
    ]);
});
