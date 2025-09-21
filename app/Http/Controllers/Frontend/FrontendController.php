<?php

namespace App\Http\Controllers\Frontend;

use App\GraphQL\Queries\CategoryQuery;
use App\GraphQL\Queries\ProductQuery;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    public function fetchCategories()
    {
        $resolver = new CategoryQuery();
        $data = $resolver->fetchCategories();
        return ResponseHelper::success($data, $message = "Categories fetched successfully...");
    }

    public function fetchProducts()
    {
        $resolver = new ProductQuery();
        $data = $resolver->fetchProducts();
        return ResponseHelper::success($data, $message = "Products fetched successfully...");
    }


    public function productDetails($slug)
    {
        $resolver = new ProductQuery();
        $data = $resolver->productDetails($slug);
        return ResponseHelper::success($data, $message = "Product fetched successfully...");
    }
}
