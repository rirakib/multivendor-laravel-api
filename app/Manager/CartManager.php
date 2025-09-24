<?php

namespace App\Manager;

use App\Helpers\GlobalHelper;
use App\Models\Frontend\Cart;
use App\Models\Frontend\CartItem;
use App\Models\Frontend\CartReservation;
use App\Models\Product\Product;
use App\Models\Product\ProductAttribute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CartManager
{
    /**
     * Get cart cache key for user or guest
     */
    protected static function cacheKey()
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        return $userId ? "cart_user_{$userId}" : "cart_session_{$sessionId}";
    }

    /**
     * Add product to cart (with attributes)
     */
    public static function addToCart(array $data)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        $cacheKey = self::cacheKey();

        return DB::transaction(function () use ($userId, $sessionId, $data, $cacheKey) {

            // Cart তৈরি বা বের করো
            $cart = Cart::firstOrCreate([
                'user_id'     => $userId,
                'guest_token' => $userId ? null : $sessionId,
            ]);

            $isAttribute = isset($data['attribute_id']);
            $availableQty = 0;

            // Stock lock check
            if ($isAttribute) {
                $attrStock = ProductAttribute::where('product_id', $data['product_id'])
                    ->where('id', $data['attribute_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($attrStock->stock_quantity < $data['quantity']) {
                    throw new \Exception("Only {$attrStock->stock_quantity} items available for this variant.");
                }

                $availableQty = $attrStock->stock_quantity;
                $price = $attrStock->price ?? $data['price'] ?? 0;
            } else {
                $product = Product::where('id', $data['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock_quantity < $data['quantity']) {
                    throw new \Exception("Only {$product->stock_quantity} items available in stock.");
                }

                $availableQty = $product->stock_quantity;
                $price = $product->price ?? $data['price'] ?? 0;
            }

            // Cart item বের করো বা নতুন তৈরি করো
            $cartItem = CartItem::firstOrNew([
                'cart_id'             => $cart->id,
                'product_id'          => $data['product_id'],
                'product_attribute_id' => $isAttribute ? $data['attribute_id'] : null,
            ]);

            // Quantity যোগ করো
            $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $data['quantity'];

            // Stock limit check
            if ($cartItem->quantity > $availableQty) {
                $cartItem->quantity = $availableQty;
            }

            $cartItem->price = $price;
            $cartItem->save();

            // Reservation update করো
            CartReservation::updateOrCreate(
                [
                    'cart_item_id'        => $cartItem->id,
                    'product_id'          => $data['product_id'],
                    'product_attribute_id' => $isAttribute ? $data['attribute_id'] : null,
                ],
                [
                    'quantity'       => $cartItem->quantity,
                    'reserved_until' => now()->addMinutes(30),
                ]
            );

            GlobalHelper::auditLog('add_to_cart', $cartItem, [], $cartItem->toArray());
            Cache::forget($cacheKey);

            return $cartItem;
        });
    }

    /**
     * Update cart item quantity
     */
    public static function updateCart(array $data)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        $cacheKey = self::cacheKey();

        return DB::transaction(function () use ($userId, $sessionId, $data, $cacheKey) {

            $cartItem = CartItem::where('id', $data['id'])
                ->whereHas('cart', function ($q) use ($userId, $sessionId) {
                    $q->when($userId, fn($q2) => $q2->where('user_id', $userId))
                        ->when(!$userId, fn($q2) => $q2->where('guest_token', $sessionId));
                })
                ->firstOrFail();

            $oldValues = $cartItem->toArray();
            $cartItem->quantity = $data['quantity'];
            $cartItem->save();

            GlobalHelper::auditLog('update_cart', $cartItem, $oldValues, $cartItem->toArray());
            Cache::forget($cacheKey);

            return $cartItem;
        });
    }

    /**
     * Remove item from cart
     */
    public static function removeFromCart($cartItemId)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        $cacheKey = self::cacheKey();

        return DB::transaction(function () use ($userId, $sessionId, $cartItemId, $cacheKey) {

            $cartItem = CartItem::where('id', $cartItemId)
                ->whereHas('cart', function ($q) use ($userId, $sessionId) {
                    $q->when($userId, fn($q2) => $q2->where('user_id', $userId))
                        ->when(!$userId, fn($q2) => $q2->where('guest_token', $sessionId));
                })
                ->firstOrFail();

            GlobalHelper::auditLog('remove_from_cart', $cartItem, $cartItem->toArray(), []);
            $cartItem->delete();

            Cache::forget($cacheKey);
            return true;
        });
    }

    /**
     * Merge guest cart into authenticated user cart
     */
    public static function mergeGuestCartToUser()
    {
        if (!Auth::check()) return;

        $userId = Auth::id();
        $sessionId = session()->getId();
        $cacheKeyUser = "cart_user_{$userId}";
        $cacheKeySession = "cart_session_{$sessionId}";

        DB::transaction(function () use ($userId, $sessionId) {
            $guestCart = Cart::where('guest_token', $sessionId)->first();

            if (!$guestCart) return;

            // User cart বের করো
            $userCart = Cart::firstOrCreate(['user_id' => $userId]);

            foreach ($guestCart->items as $item) {
                $cartItem = CartItem::firstOrNew([
                    'cart_id'             => $userCart->id,
                    'product_id'          => $item->product_id,
                    'product_attribute_id' => $item->product_attribute_id,
                ]);

                $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $item->quantity;
                $cartItem->price = $item->price;
                $cartItem->save();
            }

            // Guest cart delete করো
            $guestCart->delete();
        });

        Cache::forget($cacheKeyUser);
        Cache::forget($cacheKeySession);
    }
}
