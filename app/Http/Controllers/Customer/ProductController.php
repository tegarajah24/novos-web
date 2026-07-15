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

        $categories = Category::whereNotIn('name', ['Jersey', 'Bawahan', 'Jaket'])
            ->orderBy('name')
            ->get()
            ->map(fn($c) => [
                'slug' => Str::slug($c->name),
                'name' => $c->name,
            ]);

        return view('customer.katalog', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_if(!$product->is_active, 404);

        $product->load('category');

        $imageUrl         = $product->image         ? asset('storage/' . $product->image)         : null;
        $imageBelakangUrl = $product->image_belakang ? asset('storage/' . $product->image_belakang) : null;

        $wishlisted = auth()->check()
            ? \App\Models\Wishlist::where('product_id', $product->id)->where('user_id', auth()->id())->exists()
            : false;

        $avgRating   = round((float) (\App\Models\ProductRating::where('product_id', $product->id)->avg('rating') ?? 0), 1);
        $ratingCount = \App\Models\ProductRating::where('product_id', $product->id)->count();
        $userRating  = auth()->check()
            ? (int) (\App\Models\ProductRating::where('product_id', $product->id)->where('user_id', auth()->id())->value('rating') ?? 0)
            : 0;

        $reviews = \App\Models\ProductRating::with('user:id,name')
            ->where('product_id', $product->id)
            ->latest()
            ->take(20)
            ->get()
            ->map(fn($r) => [
                'user'    => $r->user?->name ?? 'Anonim',
                'rating'  => (int) $r->rating,
                'comment' => $r->comment ?? '',
                'date'    => $r->created_at->diffForHumans(),
            ]);

        $minQty = $product->min_qty ?? 1;

        $referensiUkuranMap = [
            'REGULER'         => '/images/referensi-ukuran/REGCUT-NVS-2026.png',
            'SLIMFIT CEWE'    => '/images/referensi-ukuran/WMNSLMCUT-NVS-2026.png',
            'OVERSIZE'        => '/images/referensi-ukuran/OVRCUT-NVS-2026.png',
            'TUNIK'           => '/images/referensi-ukuran/TUNIKCUT-NVS-2026.png',
            'SLIM FIT UNISEX' => '/images/referensi-ukuran/UNISEXSLMCUT-NVS-2026.png',
            'BOXY CUT'        => '/images/referensi-ukuran/BOXYCUT-NVS-2026.png',
            'KIDS'            => '/images/referensi-ukuran/KIDSCUT-NVS-2026.png',
        ];

        $jenisPotongan = $product->jenis_potongan;
        $referensiUkuranUrl = isset($referensiUkuranMap[$jenisPotongan])
            ? asset($referensiUkuranMap[$jenisPotongan])
            : asset('/images/referensi-ukuran/REGCUT-NVS-2026.png');

        $fullStars  = (int) floor($avgRating);
        $halfStar   = ($avgRating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        return view('customer.produk-detail', compact(
            'product', 'imageUrl', 'imageBelakangUrl',
            'minQty', 'wishlisted', 'avgRating', 'ratingCount', 'userRating', 'reviews',
            'fullStars', 'halfStar', 'emptyStars', 'referensiUkuranUrl'
        ));
    }
}
