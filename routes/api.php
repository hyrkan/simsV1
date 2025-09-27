<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicTermController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\SubjectController;
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

// Departments API Routes
Route::prefix('departments')->group(function () {
    Route::get('/', [DepartmentController::class, 'apiIndex']);
    Route::post('/', [DepartmentController::class, 'apiStore']);
    Route::put('/{department}', [DepartmentController::class, 'update']);
    Route::get('/options', [DepartmentController::class, 'getOptions']);
    Route::patch('/{department}/archive', [DepartmentController::class, 'archive']);
    Route::patch('/{department}/restore', [DepartmentController::class, 'restore']);
});

// Programs API Routes
Route::prefix('programs')->group(function () {
    Route::get('/', [ProgramController::class, 'getData']);
    Route::post('/', [ProgramController::class, 'store']);
    Route::get('/{program}', [ProgramController::class, 'show']);
    Route::put('/{program}', [ProgramController::class, 'update']);
    Route::delete('/{program}', [ProgramController::class, 'destroy']);
    Route::get('/departments/options', [ProgramController::class, 'getDepartments']);
    Route::patch('/{program}/archive', [ProgramController::class, 'archive']);
    Route::patch('/{program}/restore', [ProgramController::class, 'restore']);
    
    // Program-Curriculum relationship routes
    Route::get('/{program}/curricula', [ProgramController::class, 'getCurricula']);
});

// Curricula API Routes
Route::prefix('curricula')->group(function () {
    Route::get('/', [CurriculumController::class, 'index']);
    Route::post('/', [CurriculumController::class, 'store']);
    Route::get('/{curriculum}', [CurriculumController::class, 'show']);
    Route::put('/{curriculum}', [CurriculumController::class, 'update']);
    Route::delete('/{curriculum}', [CurriculumController::class, 'destroy']);
    Route::patch('/{curriculum}', [CurriculumController::class, 'archive']);
    
    // Curriculum-Subject relationship routes
    Route::get('/{curriculum}/subjects', [CurriculumController::class, 'getSubjects']);
    Route::post('/{curriculum}/subjects', [CurriculumController::class, 'attachSubject']);
    Route::put('/{curriculum}/subjects/{subject}', [CurriculumController::class, 'updateSubject']);
    Route::delete('/{curriculum}/subjects/{subject}', [CurriculumController::class, 'detachSubject']);
});

// Subjects API Routes
Route::prefix('subjects')->group(function () {
    Route::get('/', [SubjectController::class, 'index']);
    Route::post('/', [SubjectController::class, 'store']);
    Route::get('/{subject}', [SubjectController::class, 'show']);
    Route::put('/{subject}', [SubjectController::class, 'update']);
    Route::delete('/{subject}', [SubjectController::class, 'destroy']);
    Route::patch('/{subject}/archive', [SubjectController::class, 'archive']);
});
