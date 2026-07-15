<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

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
                $images = $product->images ?? [];
                if (empty($images)) {
                    $legacy = [];
                    if ($product->image) $legacy[] = $product->image;
                    if ($product->image_belakang) $legacy[] = $product->image_belakang;
                    $images = $legacy;
                }
                return [
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'category_id'    => $product->category_id,
                    'price'          => (int) $product->price,
                    'description'    => $product->description ?? '',
                    'images'         => array_map(fn($img) => asset('storage/' . $img), $images),
                    'kerah'          => $product->kerah,
                    'bahan'          => $product->bahan,
                    'jenis_potongan' => $product->jenis_potongan,
                    'lengan_jahitan' => $product->lengan_jahitan,
                    'product_attributes' => $product->product_attributes ?? [],
                ];
            })
            ->values()
            ->toArray();

        return view('internal.kelola-produk', compact('categories', 'products'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = app(ImageService::class)->compressAndStore($file, 'products');
            }
        }
        $data['images'] = $images;

        $product = Product::create($data);

        $productImages = array_map(fn($img) => asset('storage/' . $img), $product->images ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'product' => [
                'id'             => $product->id,
                'name'           => $product->name,
                'category_id'    => $product->category_id,
                'price'          => (int) $product->price,
                'description'    => $product->description ?? '',
                'images'         => $productImages,
                'kerah'          => $product->kerah,
                'bahan'          => $product->bahan,
                'jenis_potongan' => $product->jenis_potongan,
                'lengan_jahitan' => $product->lengan_jahitan,
                'product_attributes' => $product->product_attributes ?? [],
            ],
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $existingImages = $request->input('existing_images', []);

        $oldImages = $product->images ?? [];
        $removed = array_diff($oldImages, $existingImages);
        foreach ($removed as $path) {
            Storage::disk('public')->delete($path);
        }

        $images = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = app(ImageService::class)->compressAndStore($file, 'products');
            }
        }
        $data['images'] = $images;

        $product->update($data);

        $productImages = array_map(fn($img) => asset('storage/' . $img), $product->images ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'product' => [
                'id'             => $product->id,
                'name'           => $product->name,
                'category_id'    => $product->category_id,
                'price'          => (int) $product->price,
                'description'    => $product->description ?? '',
                'images'         => $productImages,
                'kerah'          => $product->kerah,
                'bahan'          => $product->bahan,
                'jenis_potongan' => $product->jenis_potongan,
                'lengan_jahitan' => $product->lengan_jahitan,
                'product_attributes' => $product->product_attributes ?? [],
            ],
        ]);
    }

    public function destroy(Product $product)
    {
        $images = $product->images ?? [];
        foreach ($images as $path) {
            Storage::disk('public')->delete($path);
        }

        $product->delete();
 
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus',
        ]);
    }
 
    public function getReferensi()
    {
        $collarOptions = json_decode(Setting::get('jersey_collar_options', json_encode([
            "O-NECK V.1", "O-NECK V.2", "O-NECK V.3", "O-NECK V.4", "V-NECK V.5", 
            "V-NECK V.1", "V-NECK V.2", "V-NECK V.3", "V-NECK V.4", "V-NECK V.5", 
            "CLASSIC V.1", "CLASSIC V.2", "CLASSIC V.3", "CLASSIC V.4", "CLASSIC V.5", 
            "V-NECK V3 TUMPUK", "TIMNAS"
        ])), true);
        $collarImage = Setting::get('jersey_collar_image', 'images/jersey_collar_guide.png');
        $collarImageUrl = (str_starts_with($collarImage, 'images/') || str_starts_with($collarImage, 'http'))
            ? asset($collarImage)
            : asset('storage/' . $collarImage);

        $bahanOptions = json_decode(Setting::get('jersey_bahan_options', json_encode([
            "BINTIK JARUM GRADE B","MILANO GRADE B","BINTIK JARUM PREMIUM","MILANO PREMIUM","RABBIT","DROPPEDDLE","SMASH","WAFFLE","EMBOSH","MICROCOOL","JAQUARD AERO","COTTON 24S","COTTON 30S","LOTTO","PARASUT","PUMA","ULTRALIGHT A","ULTRALIGHT B"
        ])), true);
        $bahanImage = Setting::get('jersey_bahan_image', 'images/Bahan Jersey.png');
        $bahanImageUrl = (str_starts_with($bahanImage, 'images/') || str_starts_with($bahanImage, 'http'))
            ? asset($bahanImage)
            : asset('storage/' . $bahanImage);

        $potonganOptions = json_decode(Setting::get('jersey_potongan_options', json_encode([
            "REGULER","SLIMFIT CEWE","OVERSIZE","TUNIK","SLIM FIT UNISEX","BOXY CUT","KIDS"
        ])), true);
        $potonganImage = Setting::get('jersey_potongan_image', 'images/Jenis Potongan.png');
        $potonganImageUrl = (str_starts_with($potonganImage, 'images/') || str_starts_with($potonganImage, 'http'))
            ? asset($potonganImage)
            : asset('storage/' . $potonganImage);

        $lenganOptions = json_decode(Setting::get('jersey_lengan_options', json_encode([
            "REGULER OVERDECK","REGULER PAKAI MANSET","RAGLAN A OVERDECK","RAGLAN A PAKAI MANSET","RAGLAN B OVERDECK","RAGLAN B PAKAI MANSET"
        ])), true);
        $lenganImage = Setting::get('jersey_lengan_image', 'images/Model Lengan & Jahitan.png');
        $lenganImageUrl = (str_starts_with($lenganImage, 'images/') || str_starts_with($lenganImage, 'http'))
            ? asset($lenganImage)
            : asset('storage/' . $lenganImage);

        return response()->json([
            'collar' => [
                'options' => $collarOptions,
                'image' => $collarImageUrl,
            ],
            'bahan' => [
                'options' => $bahanOptions,
                'image' => $bahanImageUrl,
            ],
            'potongan' => [
                'options' => $potonganOptions,
                'image' => $potonganImageUrl,
            ],
            'lengan' => [
                'options' => $lenganOptions,
                'image' => $lenganImageUrl,
            ]
        ]);
    }

    public function updateReferensi(Request $request)
    {
        if (auth()->user()->role->name !== 'Super Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Super Admin yang dapat mengubah referensi jersey'
            ], 403);
        }

        $type = $request->input('type'); // 'collar', 'bahan', 'potongan', 'lengan'
        if (!in_array($type, ['collar', 'bahan', 'potongan', 'lengan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe referensi tidak valid'
            ], 400);
        }

        // Simpan options jika ada
        if ($request->has('options')) {
            $options = $request->input('options');
            if (is_string($options)) {
                $options = json_decode($options, true);
            }
            if (is_array($options)) {
                Setting::set("jersey_{$type}_options", json_encode(array_values(array_filter($options))));
            }
        }

        // Simpan image jika diunggah
        if ($request->hasFile('image')) {
            $oldImage = Setting::get("jersey_{$type}_image");
            if ($oldImage && !str_starts_with($oldImage, 'images/')) {
                Storage::disk('public')->delete($oldImage);
            }
            
            $path = app(ImageService::class)->compressAndStore($request->file('image'), 'settings');
            Setting::set("jersey_{$type}_image", $path);
        }

        return response()->json([
            'success' => true,
            'message' => 'Referensi berhasil diperbarui'
        ]);
    }
}
