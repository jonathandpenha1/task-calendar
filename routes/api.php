<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/tasks', [TaskController::class, 'getByDate']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::put('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
Route::post('/tasks/filter', [TaskController::class, 'filter']);
Route::post('/tasks/{task}/toggle', [TaskController::class, 'toggleComplete']);
Route::get('/tasks/export', [TaskController::class, 'export'])->name('tasks.export');
Route::post('/tasks/import', [TaskController::class, 'import'])->name('tasks.import');
