<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesAndPermissionController;


Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::get('/admin', function () {
    return view('admin_master.layout');
})->name('admin.dashboard');


Route::get('/', function () {
    return view('landing'); 
});
Route::get('/create-staff', [AdminController::class, 'create'])->name('create-staff');
Route::post('/create-staff', [AdminController::class, 'store'])->name('create-staff.store');
Route::get('/staff-list', [AdminController::class, 'index'])->name('staff-list');
Route::get('/admin/staff', [AdminController::class, 'index'])->name('admin.staff');
Route::get('/admin/roles', [RolesAndPermissionController::class, 'index'])->name('admin.roles');

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
});

// Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function(){
//     Route::get('/create-staff', [AdminController::class, 'create'])->name('create-staff');
// });


require __DIR__.'/auth.php';
