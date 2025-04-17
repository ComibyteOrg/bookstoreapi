<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// Route::apiResource('authors', AuthorController::class);
// Route::apiResource('books', BookController::class);


Route::apiResource('books', BookController::class)
    ->only(['index', 'show']); 

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('books', BookController::class)
        ->except(['index', 'show']); 
});


Route::apiResource('authors', AuthorController::class)
    ->only(['index', 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('authors', AuthorController::class)
        ->except(['index', 'show']);
});
