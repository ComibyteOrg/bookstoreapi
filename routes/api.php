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

Route::get('/test-token', function (Request $request) {
    return [
        'authenticated' => auth('sanctum')->check(),
        'user' => $request->user(),
        'token_valid' => (bool) $request->user()->currentAccessToken()
    ];
})->middleware('auth:sanctum');


// Books routes - adjust these based on your requirements
Route::apiResource('books', BookController::class)
    ->only(['index', 'show']); // Public access to list/view books

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('books', BookController::class)
        ->except(['index', 'show']); // Protected routes for create/update/delete
});

// Similar approach for authors
Route::apiResource('authors', AuthorController::class)
    ->only(['index', 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('authors', AuthorController::class)
        ->except(['index', 'show']);
});