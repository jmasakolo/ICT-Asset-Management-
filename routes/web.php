<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks');

// index, create, store, show, edit, update, destroy
Route::resource('tasks', TaskController::class);

// One-click done/undone from the listing.
Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])
    ->name('tasks.toggle');
