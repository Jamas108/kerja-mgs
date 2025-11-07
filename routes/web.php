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
use App\Http\Controllers\Director\DirectorProfileController;
use App\Http\Controllers\Director\PromotionRequestsController;
use App\Http\Controllers\Director\ReviewController as DirectorReviewController;
use App\Http\Controllers\Director\SignatureController as DirectorSignatureController;
use App\Http\Controllers\Employee\AchievementController;
use App\Http\Controllers\HeadDivision\AchievementMemberController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\EmployeeProfileController;
use App\Http\Controllers\Employee\TaskController;
use App\Http\Controllers\HeadDivision\EmployeePerformanceController;
use App\Http\Controllers\HeadDivision\HeadDivisionProfileController;
use App\Http\Controllers\ProfileController;
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

    // Achievements
    Route::get('/achievements-member', [AchievementMemberController::class, 'index'])->name('achievements-member.index');
    Route::get('/achievements-member/{achievement}', [AchievementMemberController::class, 'show'])->name('achievements-member.show');
    Route::get('/achievements-member/{achievement}/download', [AchievementMemberController::class, 'download'])->name('achievements-member.download');

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

    Route::get('/head_division_profile', [HeadDivisionProfileController::class, 'index'])->name('head_division_profile.index');
    Route::get('/head_division_profile/edit', [HeadDivisionProfileController::class, 'edit'])->name('head_division_profile.edit');
    Route::put('/head_division_profile/update', [HeadDivisionProfileController::class, 'update'])->name('head_division_profile.update');
    Route::put('/head_division_profile/password/update', [HeadDivisionProfileController::class, 'updatePassword'])->name('head_division_profile.password.update');
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

    // Fixed route - corrected naming convention to match what's used in the view
    Route::get('/promotion-requests/{promotionRequest}/download-certificate', [PromotionRequestsController::class, 'downloadCertificate'])
        ->name('promotion_requests.download_certificate');

    // Fixed preview certificate route
    Route::post('/promotion-requests/preview-certificate', [PromotionRequestsController::class, 'previewCertificate'])
        ->name('promotion_requests.preview-certificate');

    Route::resource('signatures', DirectorSignatureController::class)->except(['show', 'create']);
    Route::patch('/signatures/{signature}/toggle-active', [DirectorSignatureController::class, 'toggleActive'])->name('signatures.toggle-active');

    Route::get('/director_profile', [DirectorProfileController::class, 'index'])->name('director_profile.index');
    Route::get('/director_profile/edit', [DirectorProfileController::class, 'edit'])->name('director_profile.edit');
    Route::put('/director_profile/update', [DirectorProfileController::class, 'update'])->name('director_profile.update');
    Route::put('/director_profile/password/update', [DirectorProfileController::class, 'updatePassword'])->name('director_profile.password.update');
});

// Employee Routes
Route::prefix('employee')->name('employee.')->middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{assignment}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{assignment}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Achievements/Certificates
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/{achievement}', [AchievementController::class, 'show'])->name('achievements.show');
    Route::get('/achievements/{achievement}/download', [AchievementController::class, 'download'])->name('achievements.download');

    Route::get('/employee_profile', [EmployeeProfileController::class, 'index'])->name('employee_profile.index');
    Route::get('/employee_profile/edit', [EmployeeProfileController::class, 'edit'])->name('employee_profile.edit');
    Route::put('/employee_profile/update', [EmployeeProfileController::class, 'update'])->name('employee_profile.update');
    Route::put('/employee_profile/password/update', [EmployeeProfileController::class, 'updatePassword'])->name('employee_profile.password.update');
});

// Route::middleware(['auth'])->group(function () {
//     // Profile Routes
//     Route::get('/head_division_profile', [ProfileController::class, 'index'])->name('head_division_profile.index');
//     Route::get('/head_division_profile/edit', [ProfileController::class, 'edit'])->name('head_division_profile.edit');
//     Route::put('/head_division_profile/update', [ProfileController::class, 'update'])->name('head_division_profile.update');
//     Route::put('/head_division_profile/password/update', [ProfileController::class, 'updatePassword'])->name('head_division_profile.password.update');
// });

// File Display Route
Route::get('/evidence/{filename}', function ($filename) {
    $path = storage_path('app/public/evidence/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->middleware('auth')->name('evidence.show');
