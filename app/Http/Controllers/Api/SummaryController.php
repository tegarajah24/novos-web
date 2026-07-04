<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Chat;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class SummaryController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'chat' => [
                'unread' => Chat::where('customer_id', $user->id)
                    ->withCount(['messages' => fn($q) => $q->where('is_read', false)->where('sender_id', '!=', $user->id)])
                    ->get()
                    ->sum('messages_count'),
            ],
            'notifikasi' => [
                'unread' => Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count(),
            ],
            'cart' => [
                'count' => Cart::where('user_id', $user->id)->count(),
            ],
        ]);
    }
}
