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
        try {
            $resolver = new CategoryQuery();
            $data = $resolver->fetchCategories();
            return ResponseHelper::success($data, $message = "Categories fetched successfully...");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function fetchProducts()
    {
        try {
            $resolver = new ProductQuery();
            $data = $resolver->fetchProducts();
            return ResponseHelper::success($data, $message = "Products fetched successfully...");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }


    public function productDetails($slug)
    {
        try {
            $resolver = new ProductQuery();
            $data = $resolver->productDetails($slug);
            return ResponseHelper::success($data, $message = "Product fetched successfully...");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function vendorWiseProduct($slug)
    {
        try {
            $resolver = new ProductQuery();
            $data = $resolver->vendorWiseProduct($slug);
            return ResponseHelper::success($data, $message = "Vendor products fetched successfully...");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }
}
