<?php

use App\Http\Controllers\Api\AssetApiController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ReportController as ApiReportController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| JSON API
|--------------------------------------------------------------------------
|
| All routes below require a Sanctum bearer token — the previous "shared,
| anonymous client" model was a documented but unacceptable exposure once
| this box became reachable from the public internet (the task list was
| the app's literal homepage, readable/writable by anyone, no login).
|
*/

Route::middleware('throttle:20,1')->post('login', [ApiAuthController::class, 'login'])
    ->name('api.login');

Route::middleware(['auth:sanctum', 'throttle:180,1'])->group(function (): void {
    Route::post('logout', [ApiAuthController::class, 'logout'])->name('api.logout');

    Route::get('tasks', [TaskApiController::class, 'index'])->name('api.tasks.index');
    Route::post('tasks', [TaskApiController::class, 'store'])->name('api.tasks.store');
    Route::get('tasks/{task}', [TaskApiController::class, 'show'])->name('api.tasks.show');
    Route::match(['put', 'patch'], 'tasks/{task}', [TaskApiController::class, 'update'])
        ->name('api.tasks.update');
    Route::delete('tasks/{task}', [TaskApiController::class, 'destroy'])
        ->name('api.tasks.destroy');
    Route::patch('tasks/{task}/toggle', [TaskApiController::class, 'toggle'])
        ->name('api.tasks.toggle');

    // Read is available to any logged-in role (ict_asset_team or manager);
    // write is restricted to ict_asset_team — managers are oversight-only.
    Route::get('assets', [AssetApiController::class, 'index'])->name('api.assets.index');
    Route::get('assets/{asset}', [AssetApiController::class, 'show'])->name('api.assets.show');

    Route::middleware('role:'.User::ROLE_ICT_ASSET_TEAM)->group(function (): void {
        Route::post('assets', [AssetApiController::class, 'store'])->name('api.assets.store');
        Route::match(['put', 'patch'], 'assets/{asset}', [AssetApiController::class, 'update'])
            ->name('api.assets.update');
    });

    Route::get('reports/pdf', [ApiReportController::class, 'pdf'])->name('api.reports.pdf');

    Route::get('users', [ApiUserController::class, 'index'])->name('api.users.index');
});
