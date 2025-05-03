<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authcontroller;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());       // ->load('profile')
    });
});



Route::post('/login', [Authcontroller::class, 'login']);
Route::post('/register', [Authcontroller::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-with-profile', [Authcontroller::class, 'userWithProfile']);
    Route::post('/logout', [Authcontroller::class, 'logout']);});

    // routes/api.php
Route::get('/api/services/search', [ServiceController::class, 'search']);



