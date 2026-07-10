<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductRating;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class ProductInteractionController extends Controller
{
    public function storeRating(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if (!auth()->check()) {
            return response()->json(['message' => 'Silakan login terlebih dahulu'], 401);
        }

        $rating = ProductRating::updateOrCreate(
            ['product_id' => $data['product_id'], 'user_id' => auth()->id()],
            ['rating' => $data['rating']]
        );

        return response()->json(['success' => true, 'rating' => $rating]);
    }

    public function toggleWishlist(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        if (!auth()->check()) {
            return response()->json(['message' => 'Silakan login terlebih dahulu'], 401);
        }

        $wishlist = Wishlist::where([
            'product_id' => $data['product_id'],
            'user_id' => auth()->id(),
        ])->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => true, 'wishlisted' => false]);
        }

        Wishlist::create([
            'product_id' => $data['product_id'],
            'user_id' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'wishlisted' => true]);
    }
}
