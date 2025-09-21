<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Frontend\FrontendController;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::prefix('admin')->group(function () {});

Route::prefix('customer')->group(function () {});


Route::prefix('vendor')->group(function () {});

Route::prefix('frontend')->group(function () {
    Route::controller(FrontendController::class)->group(function () {
        Route::get('categories', 'fetchCategories');
        Route::get('products', 'fetchProducts');
        Route::get('product/{slug}/details', 'productDetails');
    });
});
