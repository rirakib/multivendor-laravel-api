<?php

namespace App\Models\Frontend;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'guest_token',
    ];

    /**
     * একজন user বা guest এর cart items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }


    public function product(){
        return $this->belongsTo(Product::class);
    }

    /**
     * User relation (optional)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
