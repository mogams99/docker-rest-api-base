<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ! route prefix with versioning 
// Route::prefix('v1')->group(function () {
//     Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);
//     Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);
//     Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout'])->middleware('auth:sanctum');
    
//     Route::middleware('auth:sanctum')->group(function () {
//         Route::get('/user', function (Request $request) {
//             return $request->user();
//         });
//     });
// });

// ! route prefix without versioning
// ? api/products
Route::prefix('products')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\ProductController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Api\ProductController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Api\ProductController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy']);
});

// ? api/orders
Route::prefix('orders')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\OrderController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Api\OrderController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Api\OrderController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\OrderController::class, 'destroy']);
});

