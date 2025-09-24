<?php

namespace App\Models\Frontend;

use Illuminate\Database\Eloquent\Model;

class CartReservation extends Model
{
    protected $fillable = [
        'cart_item_id',
        'product_id',
        'product_attribute_id',
        'quantity',
        'reserved_until',
    ];

    /**
     * belongs to cart item
     */
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    /**
     * belongs to product
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product\Product::class);
    }

    /**
     * belongs to product attribute
     */
    public function attribute()
    {
        return $this->belongsTo(\App\Models\Product\ProductAttribute::class, 'product_attribute_id');
    }
}
