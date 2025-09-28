<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesAndPermissionController;
use App\Http\Controllers\AcademicTermController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\ExamController;
Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');

//student side
Route::get('/', function () {
    return view('landing'); 
});

Route::get('/admission', function () {
    return view('admission');
})->name('admission');














Route::get('/admin', function () {
    return view('admin_master.layout');
})->name('admin.dashboard');

//staff
Route::get('/create-staff', [AdminController::class, 'create'])->name('create-staff');
Route::post('/create-staff', [AdminController::class, 'store'])->name('create-staff.store');
Route::get('/staff-list', [AdminController::class, 'index'])->name('staff-list');
Route::get('/admin/staff', [AdminController::class, 'index'])->name('admin.staff');
Route::get('/admin/roles', [RolesAndPermissionController::class, 'index'])->name('admin.roles');

Route::resource('/admin/academic-terms', AcademicTermController::class)->middleware('auth');
Route::get('/admin/academic-terms-archive', [AcademicTermController::class, 'archiveTerms'])->name('admin.academic-terms.archive')->middleware('auth');
Route::resource('/admin/departments', DepartmentController::class)->middleware('auth');
Route::resource('/admin/programs', ProgramController::class)->middleware('auth');
Route::get('/admin/programs-archive', [ProgramController::class, 'archivePrograms'])->name('admin.programs.archive')->middleware('auth');
Route::get('/admin/programs-restore', [ProgramController::class, 'restorePrograms'])->name('admin.programs.restore')->middleware('auth');
Route::get('/admin/program/{program}', [ProgramController::class, 'showProgram'])->name('admin.program.show')->middleware('auth');

Route::resource('/admin/curricula', CurriculumController::class)->middleware('auth');
Route::resource('/admin/exams', ExamController::class)->middleware('auth');



// API Routes for Roles and Permissions
Route::prefix('admin/api')->group(function() {
    // Roles API
    Route::get('/roles', [RolesAndPermissionController::class, 'getRoles']);
    Route::post('/roles', [RolesAndPermissionController::class, 'createRole']);
    Route::delete('/roles/{id}', [RolesAndPermissionController::class, 'deleteRole']);
    Route::get('/roles/{id}/permissions', [RolesAndPermissionController::class, 'getRolePermissions']);
    Route::put('/roles/{id}/permissions', [RolesAndPermissionController::class, 'updateRolePermissions']);
    
    // Permissions API
    Route::get('/permissions', [RolesAndPermissionController::class, 'getPermissions']);
    Route::post('/permissions', [RolesAndPermissionController::class, 'createPermission']);
    Route::delete('/permissions/{id}', [RolesAndPermissionController::class, 'deletePermission']);
    
    // Exams API
    Route::get('/exams', [ExamController::class, 'getData']);
});




require __DIR__.'/auth.php';
