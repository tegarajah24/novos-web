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

        return view('customer.beranda', compact(
            'bestSellers', 'latestProducts', 'totalOrders', 'totalProducts'
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
