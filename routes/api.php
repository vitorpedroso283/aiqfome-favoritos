<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CustomerController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('customers')->group(function () {
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/', [CustomerController::class, 'index']);
        Route::put('/{id}', [CustomerController::class, 'update']);
        Route::get('/{id}', [CustomerController::class, 'show']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
    });
});
