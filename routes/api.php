<?php

use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API V1 Routes
Route::prefix('v1')->group(function () {
    // User Routes
    Route::post('users/register', [UserController::class, 'register']);
    Route::post('users/login', [UserController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:api')->group(function () {
        // User Routes
        Route::post('users/logout', [UserController::class, 'logout']);
        Route::get('users/me', [UserController::class, 'me']);

        // Product Routes
        Route::get('products/search', [ProductController::class, 'search']);
        Route::apiResource('products', ProductController::class);
    });
});