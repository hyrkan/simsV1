<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin', function () {
    return view('admin_master.layout'); 
});

Route::get('/landing', function () {
    return view('landing'); 
});
Route::get('/create-staff', [AdminController::class, 'create'])->name('create-staff');
// Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function(){
//     Route::get('/create-staff', [AdminController::class, 'create'])->name('create-staff');
// });


require __DIR__.'/auth.php';
