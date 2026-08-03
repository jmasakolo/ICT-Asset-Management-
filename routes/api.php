<?php

use App\Http\Controllers\Api\TaskApiController;
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
