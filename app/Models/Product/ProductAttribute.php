<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    //

    protected $casts = [
        'price_modifier' => 'float',
        'stock_quantity' => 'integer'
    ];
}
