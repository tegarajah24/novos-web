<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'category' => $p->category?->name ?? 'Katalog',
                'price'    => $p->price ? (int) $p->price : null,
                'badge'    => null,
                'image'    => $p->image ? asset('storage/' . $p->image) : null,
            ]);

        return view('customer.katalog', compact('products'));
    }
}
