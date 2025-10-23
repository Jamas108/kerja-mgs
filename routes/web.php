<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SignatureController;
use App\Http\Controllers\HeadDivision\DashboardController as HeadDivisionDashboardController;
use App\Http\Controllers\HeadDivision\JobDeskController;
use App\Http\Controllers\HeadDivision\ReviewController as HeadDivisionReviewController;
use App\Http\Controllers\Director\DashboardController as DirectorDashboardController;
use App\Http\Controllers\Director\PromotionRequestsController;
use App\Http\Controllers\Director\ReviewController as DirectorReviewController;
use App\Http\Controllers\Director\SignatureController as DirectorSignatureController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\TaskController;
use App\Http\Controllers\HeadDivision\EmployeePerformanceController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Register middleware in App\Http\Kernel.php
// protected $routeMiddleware = [
//     ...
//     'role' => \App\Http\Middleware\CheckRole::class,
// ];

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', UserController::class);

    // Division Management
    Route::resource('divisions', DivisionController::class);

    // Role Management
    Route::resource('roles', RoleController::class);
});

// Head Division Routes
Route::prefix('head-division')->name('head_division.')->middleware(['auth', 'role:kepala divisi'])->group(function () {
    Route::get('/dashboard', [HeadDivisionDashboardController::class, 'index'])->name('dashboard');

    // Employee Management
    Route::get('/team-members', [App\Http\Controllers\HeadDivision\TeamMembersController::class, 'index'])->name('team_members.index');
    Route::get('/team-members/{member}', [App\Http\Controllers\HeadDivision\TeamMembersController::class, 'show'])->name('team_members.show');

    // Job Desk Management
    Route::resource('job_desks', JobDeskController::class);

    // Review Assignments
    Route::get('/reviews', [HeadDivisionReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{assignment}', [HeadDivisionReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{assignment}', [HeadDivisionReviewController::class, 'review'])->name('reviews.submit');

    Route::get('/performances', [App\Http\Controllers\HeadDivision\EmployeePerformanceController::class, 'index'])
        ->name('performances.index');
    Route::get('/performances/{id}', [App\Http\Controllers\HeadDivision\EmployeePerformanceController::class, 'show'])
        ->name('performances.show');
    Route::get('/performances-compare', [App\Http\Controllers\HeadDivision\EmployeePerformanceController::class, 'compare'])
        ->name('performances.compare');
    Route::post('/performances-compare', [App\Http\Controllers\HeadDivision\EmployeePerformanceController::class, 'compare'])
        ->name('performances.compare.post');
    Route::get('/performances-report', [App\Http\Controllers\HeadDivision\EmployeePerformanceController::class, 'report'])
        ->name('performances.report');

    Route::get('/performances/{employee}/propose-promotion', [EmployeePerformanceController::class, 'proposePromotion'])
        ->name('performances.propose_promotion');
    Route::post('/performances/{employee}/propose-promotion', [EmployeePerformanceController::class, 'storePromotion'])
        ->name('performances.store_promotion');
});

// Director Routes
Route::prefix('director')->name('director.')->middleware(['auth', 'role:direktur'])->group(function () {
    Route::get('/dashboard', [DirectorDashboardController::class, 'index'])->name('dashboard');

    // Review Assignments
    Route::get('/reviews', [DirectorReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{assignment}', [DirectorReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{assignment}', [DirectorReviewController::class, 'review'])->name('reviews.submit');

    Route::get('/promotion-requests', [PromotionRequestsController::class, 'index'])->name('promotion_requests.index');
    Route::get('/promotion-requests/{promotionRequest}', [PromotionRequestsController::class, 'show'])->name('promotion_requests.show');
    Route::post('/promotion-requests/{promotionRequest}/approve', [PromotionRequestsController::class, 'approve'])->name('promotion_requests.approve');
    Route::post('/promotion-requests/{promotionRequest}/reject', [PromotionRequestsController::class, 'reject'])->name('promotion_requests.reject');
    Route::get('promotion-requests/{promotionRequest}/download-certificate', 'App\Http\Controllers\Director\PromotionRequestsController@downloadCertificate')
        ->name('promotion_requests.download_certificate');

    Route::get('/signatures', [DirectorSignatureController::class, 'index'])->name('signatures.index');
    Route::post('/signatures', [DirectorSignatureController::class, 'store'])->name('signatures.store');

    // Route::resource('signatures', SignatureController::class)
    //     ->except(['show', 'create']);

    Route::patch('signatures/{signature}/toggle-active', 'App\Http\Controllers\Admin\SignatureController@toggleActive')
        ->name('signatures.toggle-active');
});

// Employee Routes
Route::prefix('employee')->name('employee.')->middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{assignment}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{assignment}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
});

// File Display Route
Route::get('/evidence/{filename}', function ($filename) {
    $path = storage_path('app/public/evidence/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->middleware('auth')->name('evidence.show');
