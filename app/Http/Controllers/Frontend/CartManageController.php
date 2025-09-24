<?php

namespace App\Http\Controllers\Frontend;

use App\GraphQL\Queries\CartQuery;
use App\Helpers\ResponseHelper;
use App\Manager\CartManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class CartManageController extends Controller
{
    /**
     * Fetch all cart items
     */
    public function index()
    {
        try {
            $resolver = new CartQuery();
            $data = $resolver->fetchItems();
            return ResponseHelper::success($data, "Cart items fetched successfully.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    /**
     * Add a product to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'attribute_id' => [
                'sometimes',
                'integer',

                Rule::exists('product_attributes', 'id')
                    ->where(function ($query) use ($request) {
                        if ($request->filled('product_id')) {
                            $query->where('product_id', $request->product_id);
                        }
                    }),
            ],
        ]);

        $cartItem = CartManager::addToCart($request->only('product_id', 'quantity', 'attribute_id'));

        try {


            return ResponseHelper::success($cartItem, "Product added to cart.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }


    public function updateCart(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $cartItem = CartManager::updateCart([
                'id' => $id,
                'quantity' => $request->quantity
            ]);

            return ResponseHelper::success($cartItem, "Cart item updated.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }


    public function removeFromCart($id)
    {
        try {
            CartManager::removeFromCart($id);
            return ResponseHelper::success(null, "Cart item removed.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }
}
