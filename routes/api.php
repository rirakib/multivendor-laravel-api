<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::prefix('admin')->group(function () {});

Route::prefix('customer')->group(function () {});


Route::prefix('vendor')->group(function () {});

