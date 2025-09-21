<?php

namespace App\GraphQL\Queries;

use App\Helpers\GlobalPaginator;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Cache;

class ProductQuery
{
    /**
     * Fetch categories with children recursively.
     *
     * @return \Illuminate\Support\Collection
     */
    public function fetchProducts()
    {

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 20);

        return Cache::remember("products_page_{$page}_{$perPage}", 3600, function () use ($page, $perPage) {
            $query = Product::active()
                ->inStock()
                ->select([
                    'id',
                    'name',
                    'slug',
                    'vendor_id',
                    'category_id',
                    'brand_id',
                    'price',
                    'discount_price',
                    'sku',
                    'stock_quantity',
                    'thumbnail_id'
                ])
                ->with([
                    'thumbnailImage:id,image',
                    'category:id,name,slug',
                    'brand:id,name,slug',
                    'vendor:id,shop_name,shop_slug'
                ])
                ->orderBy('id');

            // get collection
            $products = $query->get();

            // convert to array before passing to paginator
            $paginated = GlobalPaginator::paginateCollection(collect($products), $perPage, $page);

            return GlobalPaginator::format($paginated);
        });
    }


    public function productDetails($slug)
    {
   
        return Cache::remember("product_details_{$slug}", 3600, function () use ($slug) {

            // Fetch the product with related data
            $product = Product::active()
                ->with([
                    'thumbnailImage:id,product_id,image',
                    'category:id,name,slug',
                    'brand:id,name,slug',
                    'vendor:id,shop_name,shop_slug',
                    'images:id,image',
                    'attributes:id,product_id,attribute_name,attribute_value,price_modifier,stock_quantity'
                ])
                ->where('slug', $slug)
                ->first();

            if (!$product) {
                abort(404, 'Product not found');
            }

            // Optionally format product data
            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'vendor' => $product->vendor,
                'category' => $product->category,
                'brand' => $product->brand,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'thumbnail' => $product->thumbnailImage,
                'images' => $product->images ?? [],
                'reviews' => $product->reviews ?? [],
                'attributes' => $product->attributes
                    ->groupBy('attribute_name')
                    ->map(function ($item) {
                        return $item->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'value' => $item->attribute_value,
                                'price_modifier' => $item->price_modifier,
                                'stock_quantity' => $item->stock_quantity,
                            ];
                        });
                    })
            ];

            return $data;
        });
    }
}
