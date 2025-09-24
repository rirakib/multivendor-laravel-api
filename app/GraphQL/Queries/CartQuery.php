<?php

namespace App\GraphQL\Queries;


use App\Models\Frontend\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CartQuery
{
    /**
     * Fetch categories with children recursively.
     *
     * @return \Illuminate\Support\Collection
     */
    public function fetchItems()
    {
        $userId = Auth::id();
        $guestToken = request()->session()->getId();

        $cacheKey = $userId
            ? "cart_user_{$userId}"
            : "cart_session_{$guestToken}";
        Cache::clear();
        return Cache::remember($cacheKey, 3600, function () use ($userId, $guestToken) {
            return Cart::with([
                'items.product:id,name,stock_quantity,price',
                'items.attribute:id,product_id,attribute_name,stock_quantity,attribute_value',
                'items.reservation'
            ])
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->when(!$userId, fn($q) => $q->where('guest_token', $guestToken))
                ->first();
        });
    }
}
