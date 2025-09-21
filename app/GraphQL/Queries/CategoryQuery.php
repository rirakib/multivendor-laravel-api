<?php

namespace App\GraphQL\Queries;


use App\Helpers\GlobalPaginator;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Cache;

class CategoryQuery
{
    /**
     * Fetch categories with children recursively.
     *
     * @return \Illuminate\Support\Collection
     */
    public function fetchCategories()
    {

        return Cache::remember('categories_tree', 3600, function () {
            $categories = Category::whereHas('products', fn($q) => $q->active())
                ->get(['id', 'parent_id', 'name', 'slug', 'description']);
            $categoriesGrouped = $categories->groupBy('parent_id');
            return GlobalPaginator::paginate($this->buildCategoryTree($categoriesGrouped));
        });
    }

    /**
     * Recursively build category tree.
     *
     * @param  \Illuminate\Support\Collection  $categoriesGrouped
     * @param  int|null  $parentId
     * @return \Illuminate\Support\Collection
     */
    private function buildCategoryTree($categoriesGrouped, $parentId = null)
    {
        return ($categoriesGrouped[$parentId] ?? collect())->map(function ($cat) use ($categoriesGrouped) {
            $cat->children = $this->buildCategoryTree($categoriesGrouped, $cat->id);
            return $cat;
        });
    }


    public function categoryWiseProduct($slug)
    {

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 20);


        $cacheKey = "category_{$slug}_products_page_{$page}_{$perPage}";

        return Cache::remember($cacheKey, 3600, function () use ($slug, $page, $perPage) {

            // Build query
            $query = Product::query()
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
                    'thumbnail_id',
                ])
                ->active()
                ->inStock()
                ->whereHas('category', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })->whereHas('vendor', function ($q) {
                    $q->approved();
                })
                ->with([
                    'thumbnailImage:id,image',
                    'category:id,name,slug',
                    'brand:id,name,slug',
                    'vendor:id,shop_name,shop_slug'
                ])
                ->orderBy('id');

            $productsCollection = $query->get();

            $paginated = GlobalPaginator::paginateCollection(collect($productsCollection), $perPage, $page);

            return GlobalPaginator::format($paginated);
        });
    }
}
