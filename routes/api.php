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
