<?php

use App\Http\Controllers\Admin\AssetController as AdminAssetController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenanceController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks');

// Single login page for both account types (admin, regular user).
// AuthController::login tries the admin guard then the web guard and
// redirects by whichever one actually authenticated.
Route::middleware('guest:web,admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:20,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // Formerly public with no login at all — this was the app's homepage,
    // readable/writable by any anonymous visitor. Now behind the same
    // session guard as the rest of the regular-user area.
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])
        ->name('tasks.toggle');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // No separate admin login page anymore — send anyone who lands here
    // (an old bookmark, a link) to the single unified /login instead.
    Route::redirect('/login', '/login')->name('login');

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/assets', [AdminAssetController::class, 'index'])->name('assets.index');
        Route::post('/assets', [AdminAssetController::class, 'store'])->name('assets.store');
        Route::put('/assets/{asset}', [AdminAssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AdminAssetController::class, 'destroy'])->name('assets.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [AdminDepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/locations', [AdminLocationController::class, 'index'])->name('locations.index');
        Route::post('/locations', [AdminLocationController::class, 'store'])->name('locations.store');
        Route::put('/locations/{location}', [AdminLocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}', [AdminLocationController::class, 'destroy'])->name('locations.destroy');

        Route::get('/maintenance', [AdminMaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance', [AdminMaintenanceController::class, 'store'])->name('maintenance.store');
        Route::put('/maintenance/{maintenance}', [AdminMaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('/maintenance/{maintenance}', [AdminMaintenanceController::class, 'destroy'])->name('maintenance.destroy');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [AdminReportController::class, 'pdf'])->name('reports.pdf');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    });
});
