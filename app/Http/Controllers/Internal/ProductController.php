<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'category_id'    => $product->category_id,
                    'price'          => (int) $product->price,
                    'description'    => $product->description ?? '',
                    'image_depan'    => $product->image ? asset('storage/' . $product->image) : null,
                    'image_belakang' => null,
                    'is_featured'    => $product->is_featured ?? false,
                    'kerah'          => $product->kerah,
                    'bahan'          => $product->bahan,
                    'jenis_potongan' => $product->jenis_potongan,
                    'lengan_jahitan' => $product->lengan_jahitan,
                ];
            })
            ->values()
            ->toArray();

        return view('internal.kelola-produk', compact('categories', 'products'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'product' => [
                'id'             => $product->id,
                'name'           => $product->name,
                'category_id'    => $product->category_id,
                'price'          => (int) $product->price,
                'description'    => $product->description ?? '',
                'image_depan'    => $product->image ? asset('storage/' . $product->image) : null,
                'image_belakang' => null,
                'is_featured'    => $product->is_featured ?? false,
                'kerah'          => $product->kerah,
                'bahan'          => $product->bahan,
                'jenis_potongan' => $product->jenis_potongan,
                'lengan_jahitan' => $product->lengan_jahitan,
            ],
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_featured'] = $request->boolean('is_featured');

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'product' => [
                'id'             => $product->id,
                'name'           => $product->name,
                'category_id'    => $product->category_id,
                'price'          => (int) $product->price,
                'description'    => $product->description ?? '',
                'image_depan'    => $product->image ? asset('storage/' . $product->image) : null,
                'image_belakang' => null,
                'is_featured'    => $product->is_featured ?? false,
                'kerah'          => $product->kerah,
                'bahan'          => $product->bahan,
                'jenis_potongan' => $product->jenis_potongan,
                'lengan_jahitan' => $product->lengan_jahitan,
            ],
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus',
        ]);
    }

    public function toggleFeatured(Product $product)
    {
        $newValue = !$product->is_featured;

        if ($newValue) {
            Product::where('is_featured', true)->update(['is_featured' => false]);
        }

        $product->update(['is_featured' => $newValue]);

        return response()->json([
            'success' => true,
            'is_featured' => $newValue,
            'message' => $newValue
                ? "{$product->name} sekarang menjadi produk utama"
                : "{$product->name} tidak lagi menjadi produk utama",
        ]);
    }
}
