<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Http\Controllers\Frontend\CartManageController;
use App\Http\Controllers\Frontend\FrontendController;



Route::controller(FrontendController::class)->group(function () {
    Route::get('categories', 'fetchCategories');
    Route::get('products', 'fetchProducts');
    Route::get('product/{slug}/details', 'productDetails');
    Route::get('products/{slug}/vendor', 'vendorWiseProduct');
    Route::get('products/{slug}/category', 'categoryWiseProduct');
    Route::get('products/search', 'productSearch');
});

Route::middleware([StartSession::class, AddQueuedCookiesToResponse::class])
    ->controller(CartManageController::class)->group(function () {
        Route::get('/cart', 'index');
        Route::post('/cart/add', 'addToCart');
        Route::put('/cart/{id}', 'updateCart');
        Route::delete('/cart/{id}', 'removeFromCart');
    });
