<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authenticated routes
Route::middleware('auth:sanctum')->group( function() {
    
    // region Questions
    Route::post('questions', fn() => '')->name('questions.store');

    // end region Questions
});
