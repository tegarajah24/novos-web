<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'category'       => $p->category?->name ?? 'Katalog',
                'price'          => $p->price ? (int) $p->price : null,
                'badge'          => null,
                'image'          => $p->image ? asset('storage/' . $p->image) : null,
                'image_belakang' => $p->image_belakang ? asset('storage/' . $p->image_belakang) : null,
                'kerah'          => $p->kerah,
                'bahan'          => $p->bahan,
                'jenis_potongan' => $p->jenis_potongan,
                'lengan_jahitan' => $p->lengan_jahitan,
            ]);

        $categories = Category::orderBy('name')->get()->map(fn($c) => [
            'slug' => Str::slug($c->name),
            'name' => $c->name,
        ]);

        return view('customer.katalog', compact('products', 'categories'));
    }
}
