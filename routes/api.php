<?php

use App\Http\Controllers\Api\AssetApiController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ReportController as ApiReportController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| JSON API
|--------------------------------------------------------------------------
|
| Consumed by the Ionic client in public/app. These routes are stateless and
| carry no CSRF token — Laravel's api group has neither session nor CSRF
| middleware — so they are safe to call with plain fetch().
|
| There is no authentication, matching the web UI. The throttle is a blunt
| safety net against a runaway client, not a security control.
|
*/

Route::middleware('throttle:180,1')->group(function (): void {
    Route::get('tasks', [TaskApiController::class, 'index'])->name('api.tasks.index');
    Route::post('tasks', [TaskApiController::class, 'store'])->name('api.tasks.store');
    Route::get('tasks/{task}', [TaskApiController::class, 'show'])->name('api.tasks.show');
    Route::match(['put', 'patch'], 'tasks/{task}', [TaskApiController::class, 'update'])
        ->name('api.tasks.update');
    Route::delete('tasks/{task}', [TaskApiController::class, 'destroy'])
        ->name('api.tasks.destroy');
    Route::patch('tasks/{task}/toggle', [TaskApiController::class, 'toggle'])
        ->name('api.tasks.toggle');
});

/*
|--------------------------------------------------------------------------
| Asset intake API (mobile app)
|--------------------------------------------------------------------------
|
| Unlike the tasks API above, this one requires a login — intake staff are
| identified individuals, not an anonymous shared client — so it's token
| auth via Sanctum rather than the tasks API's "no auth" model.
|
*/

Route::middleware('throttle:20,1')->post('login', [ApiAuthController::class, 'login'])
    ->name('api.login');

Route::middleware(['auth:sanctum', 'throttle:180,1'])->group(function (): void {
    Route::post('logout', [ApiAuthController::class, 'logout'])->name('api.logout');

    Route::get('assets', [AssetApiController::class, 'index'])->name('api.assets.index');
    Route::post('assets', [AssetApiController::class, 'store'])->name('api.assets.store');
    Route::get('assets/{asset}', [AssetApiController::class, 'show'])->name('api.assets.show');
    Route::match(['put', 'patch'], 'assets/{asset}', [AssetApiController::class, 'update'])
        ->name('api.assets.update');

    Route::get('reports/pdf', [ApiReportController::class, 'pdf'])->name('api.reports.pdf');

    Route::get('users', [ApiUserController::class, 'index'])->name('api.users.index');
});
