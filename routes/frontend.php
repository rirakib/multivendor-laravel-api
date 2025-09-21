<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\FrontendController;



Route::controller(FrontendController::class)->group(function () {
    Route::get('categories', 'fetchCategories');
    Route::get('products', 'fetchProducts');
    Route::get('product/{slug}/details', 'productDetails');
    Route::get('products/{slug}/vendor', 'vendorWiseProduct');
    Route::get('products/{slug}/category', 'categoryWiseProduct');
    Route::get('products/search','productSearch');
});
