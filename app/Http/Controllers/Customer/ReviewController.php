<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pesanan ini.'
            ], 403);
        }

        if ($order->status !== 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan hanya dapat diberikan untuk pesanan yang telah selesai.'
            ], 400);
        }

        $review = Review::updateOrCreate(
            [
                'order_id' => $order->id,
            ],
            [
                'user_id' => auth()->id(),
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil disimpan.',
            'review' => $review
        ]);
    }
}
