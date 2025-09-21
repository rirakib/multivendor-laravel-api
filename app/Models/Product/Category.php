<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    function children()
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    function products()
    {
        return $this->hasMany(Product::class,'category_id');
    }
}
