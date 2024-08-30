<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


// Task routes
Route::get('/',[TaskController::class, 'index']);
Route::post('/tasks',[TaskController::class, 'store']);
Route::put('/tasks/{task}/toggle', [TaskController::class, 'toggleCompletion']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
Route::put('/tasks/{task}', [TaskController::class, 'update']);
