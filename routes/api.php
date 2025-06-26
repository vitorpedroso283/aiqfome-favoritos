<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\CustomerFavorite\CustomerFavoriteController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    Route::apiResource('customers', CustomerController::class)->only([
        'index',
        'store',
        'show',
        'update',
        'destroy'
    ]);

    Route::prefix('customers/{customer}')->group(function () {
        Route::get('favorites', [CustomerFavoriteController::class, 'index']);
        Route::post('favorites', [CustomerFavoriteController::class, 'store']);
        Route::delete('favorites/{product}', [CustomerFavoriteController::class, 'destroy']);
    });
});
