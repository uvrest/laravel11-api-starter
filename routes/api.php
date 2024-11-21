<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

//Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Private Routes (Protected by Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // User Management
    Route::get('/users', [AuthController::class, 'index']);
    Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::delete('/users/{id}', [AuthController::class, 'delete']);
    Route::patch('/users/{id}/restore', [AuthController::class, 'restore']);
    Route::delete('/users/{id}/annihilate', [AuthController::class, 'annihilate']);

    // User Profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::patch('/users/{id}/update-password', [AuthController::class, 'updatePassword']);
    Route::post('/users/{id}/update-avatar', [AuthController::class, 'updateAvatar']);
    Route::delete('/users/{id}/delete-avatar', [AuthController::class, 'deleteAvatar']);
});
