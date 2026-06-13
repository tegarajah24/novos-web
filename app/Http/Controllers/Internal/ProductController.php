<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all()->map(function ($cat) {
            return [
                'id'   => $cat->id,
                'name' => $cat->name,
            ];
        })->values()->toArray();

        $products = Product::with('category')
            ->latest()
            ->get()
            ->map(function ($product) {
                return [
                    'id'            => $product->id,
                    'name'          => $product->name,
                    'category_id'   => $product->category_id,
                    'price'         => (int) $product->price,
                    'description'   => $product->description ?? '',
                    'image_depan'   => $product->image ? asset('storage/' . $product->image) : null,
                    'image_belakang' => null,
                    'is_featured'   => false,
                ];
            })
            ->values()
            ->toArray();

        return view('internal.kelola-produk', compact('categories', 'products'));
    }
}
