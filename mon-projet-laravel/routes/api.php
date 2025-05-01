<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers;
use App\Http\Controllers\ServiceControllers;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\RequestController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//route register
Route::post('/register', [AuthControllers::class, 'register']);

//route login
Route::post('/login', [AuthControllers::class, 'login']);

Route::get('/user-with-profile', [AuthControllers::class, 'getUserWithProfile']);

// Route::prefix('auth')->group(function() {
//     Route::get('/me', [AuthControllers::class, 'me'])->middleware('auth:sanctum');
// });

//route logout
Route::post('/logout', [AuthControllers::class, 'logout'])->middleware('auth:sanctum');

// Services (accessible publiquement)
Route::get('/services', [ServiceControllers::class, 'index']);
Route::get('/services/{service}', [ServiceControllers::class, 'show']);

// Prestataires (accessible publiquement)
Route::get('/providers', [ProviderController::class, 'index']);
Route::get('/providers/{provider}', [ProviderController::class, 'show']);

// Auth

// Route::get('/me', [AuthControllers::class, 'me']);
// Route::put('/profile', 

// [AuthControllers::class, 'updateProfile']);

// Routes pour les prestataires
Route::middleware('auth:provider')->group(function () {
    Route::put('/provider/services', [ProviderController::class, 'updateServices']);
    Route::get('/provider/availabilities', [AvailabilityController::class, 'index']);
    Route::post('/provider/availabilities', [AvailabilityController::class, 'store']);
    Route::put('/provider/availabilities/{availability}', [AvailabilityController::class, 'update']);
    Route::delete('/provider/availabilities/{availability}', [AvailabilityController::class, 'destroy']);
});

 // Demandes de service
 Route::get('/requests', [RequestController::class, 'index']);
 Route::get('/requests/{request}', [RequestController::class, 'show']);

 // Routes spécifiques aux clients
 Route::middleware('auth:client')->group(function () {
    Route::post('/requests', [RequestController::class, 'store']);
});

// Routes spécifiques aux prestataires
Route::middleware('auth:provider')->group(function () {
    Route::put('/requests/{request}/status', [RequestController::class, 'updateStatus']);
});