<?php

namespace App\GraphQL\Queries;


use App\Helpers\GlobalPaginator;
use App\Models\Product\Category;
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
}
