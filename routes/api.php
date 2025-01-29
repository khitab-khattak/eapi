<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController; // Import the controller

// Test route to verify the API is working
Route::get('/test', function () {
    return "working";
});

// Route for storing user
Route::post('user/store', [UserController::class, 'store']);
Route::get('users/get/{flag}', [UserController::class,'index']);
Route::get('user/{id}', [UserController::class,'show']);
Route::delete('user/{id}', [UserController::class,'destroy']);
Route::get('user/update/{id}', [UserController::class,'update']);
Route::get('user/change-password/{id}', [UserController::class, 'changePassword']);
