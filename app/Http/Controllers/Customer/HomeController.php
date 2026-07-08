<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $bestSellers = Product::where('is_active', true)
            ->with('category')
            ->inRandomOrder()
            ->take(8)
            ->get();

        $latestProducts = Product::where('is_active', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $totalOrders = Order::where('status', 'selesai')->count();
        $totalProducts = Product::where('is_active', true)->count();

        $dbReviews = \App\Models\Review::with('user')
            ->where('rating', 5)
            ->latest()
            ->get()
            ->map(function ($rev) {
                return [
                    'rating' => $rev->rating,
                    'comment' => $rev->comment,
                    'user_name' => $rev->user->fullname ?: $rev->user->name,
                ];
            })
            ->toArray();

        $defaultReviews = [
            [
                'rating' => 5,
                'comment' => 'Jerseynya bagus banget, bahan adem dan jahitannya rapi. Desain sesuai request. Pasti order lagi!',
                'user_name' => 'Rina A.'
            ],
            [
                'rating' => 5,
                'comment' => 'Proses cepat banget, 5 hari jadi. Komunikasi dengan admin juga responsif. Recommended!',
                'user_name' => 'Dimas P.'
            ],
            [
                'rating' => 5,
                'comment' => 'Hasil jahitan rapi, sablon nempel kuat, warna sesuai mockup. Tim Novos profesional banget.',
                'user_name' => 'Sari W.'
            ]
        ];

        $allReviews = array_merge($dbReviews, $defaultReviews);

        $dbAllReviewsForModal = \App\Models\Review::with('user')
            ->latest()
            ->get()
            ->map(function ($rev) {
                return [
                    'rating' => $rev->rating,
                    'comment' => $rev->comment,
                    'user_name' => $rev->user->fullname ?: $rev->user->name,
                    'created_at' => $rev->created_at->diffForHumans(),
                ];
            })
            ->toArray();

        $defaultAllReviewsForModal = array_map(function ($r) {
            $r['created_at'] = 'Baru-baru ini';
            return $r;
        }, $defaultReviews);

        $allReviewsForModal = array_merge($dbAllReviewsForModal, $defaultAllReviewsForModal);

        $avgRating = \App\Models\Review::avg('rating') ?: 4.9;
        $formattedRating = number_format($avgRating, 1) . '★';

        return view('customer.beranda', compact(
            'bestSellers', 'latestProducts', 'totalOrders', 'totalProducts',
            'allReviews', 'allReviewsForModal', 'formattedRating'
        ));
    }

    public function tentang()
    {
        $tim = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', Role::internalNames()))
            ->orderBy('created_at')
            ->get();
        return view('customer.tentang-kami', compact('tim'));
    }
}
