<?php

use App\Http\Controllers\Admin\AssetController as AdminAssetController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks');

// index, create, store, show, edit, update, destroy
Route::resource('tasks', TaskController::class);

// One-click done/undone from the listing.
Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])
    ->name('tasks.toggle');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/assets', [AdminAssetController::class, 'index'])->name('assets.index');
        Route::post('/assets', [AdminAssetController::class, 'store'])->name('assets.store');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');

        // Placeholder sections for the admin nav shell — real modules land one at a time.
        Route::view('/departments', 'admin.placeholder', ['title' => 'Departments', 'active' => 'departments'])->name('departments.index');
        Route::view('/locations', 'admin.placeholder', ['title' => 'Locations', 'active' => 'locations'])->name('locations.index');
        Route::view('/maintenance', 'admin.placeholder', ['title' => 'Maintenance', 'active' => 'maintenance'])->name('maintenance.index');
        Route::view('/reports', 'admin.placeholder', ['title' => 'Reports', 'active' => 'reports'])->name('reports.index');
        Route::view('/audit-logs', 'admin.placeholder', ['title' => 'Audit Logs', 'active' => 'audit-logs'])->name('audit-logs.index');
        Route::view('/settings', 'admin.placeholder', ['title' => 'Settings', 'active' => 'settings'])->name('settings.index');
    });
});
