<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\StoreDesignCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $cartItems = Cart::with('product.category')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $totalSelected = $cartItems->where('is_selected', true)->sum(function ($item) {
            return $this->calculateItemTotal($item);
        });

        return response()->json([
            'items' => $cartItems,
            'total_selected' => $totalSelected,
            'count' => $cartItems->sum('qty'),
        ]);
    }

    public function count(): JsonResponse
    {
        $count = Cart::where('user_id', auth()->id())->sum('qty');
        return response()->json(['count' => $count]);
    }

    public function store(StoreCartRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->product_id);

        $cart = Cart::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'size' => $request->size,
            ],
            [
                'qty' => $request->qty,
                'is_selected' => true,
                'notes' => $request->notes,
            ]
        );

        $count = Cart::where('user_id', auth()->id())->sum('qty');

        notify()->success('Produk berhasil ditambahkan ke keranjang');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => $cart->load('product.category'),
            'count' => $count,
        ]);
    }

    public function storeDesign(StoreDesignCartRequest $request): JsonResponse
    {

        $designData = $request->design_data;
        $totalQty = $designData['total_qty'] ?? collect($designData['ukuran'] ?? [])->sum(fn($v) => (int) $v);

        $cart = Cart::create([
            'user_id' => auth()->id(),
            'product_id' => null,
            'size' => 'Custom',
            'qty' => $totalQty ?: 1,
            'is_selected' => true,
            'design_data' => $designData,
            'notes' => $request->notes,
            'image' => $request->image,
        ]);

        $count = Cart::where('user_id', auth()->id())->sum('qty');

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil disimpan ke keranjang',
            'cart' => $cart->load('product.category'),
            'count' => $count,
        ]);
    }

    public function update(UpdateCartRequest $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cart->update(['qty' => $request->qty]);

        $totalSelected = Cart::with('product')
            ->where('user_id', auth()->id())
            ->where('is_selected', true)
            ->get()
            ->sum(function ($item) {
                return $this->calculateItemTotal($item);
            });

        return response()->json([
            'success' => true,
            'message' => 'Jumlah berhasil diperbarui',
            'cart' => $cart->load('product.category'),
            'total_selected' => $totalSelected,
        ]);
    }

    public function destroy(Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        $count = Cart::where('user_id', auth()->id())->sum('qty');

        return response()->json([
            'success' => true,
            'message' => 'Produk dihapus dari keranjang',
            'count' => $count,
        ]);
    }

    public function updateSizes(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $sizes = $request->validate(['sizes' => 'required|array']);
        $sizes = $sizes['sizes'];

        $totalQty = collect($sizes)->sum(fn($v) => (int) $v);

        $designData = $cart->design_data;
        $designData['total_qty'] = $totalQty;
        $cart->update([
            'design_data' => $designData,
            'qty' => $totalQty,
        ]);

        return response()->json([
            'success' => true,
            'qty' => $totalQty,
        ]);
    }

    public function toggleSelect(Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cart->update(['is_selected' => !$cart->is_selected]);

        $totalSelected = Cart::with('product')
            ->where('user_id', auth()->id())
            ->where('is_selected', true)
            ->get()
            ->sum(function ($item) {
                return $this->calculateItemTotal($item);
            });

        return response()->json([
            'success' => true,
            'is_selected' => $cart->is_selected,
            'total_selected' => $totalSelected,
        ]);
    }

    protected function calculateItemTotal($item)
    {
        if ($item->design_data) {
            $qty = $item->design_data['total_qty'] ?? collect($item->design_data['ukuran'] ?? [])->sum(fn($v) => (int) $v);
            $basePrice = ($item->design_data['base_price_per_pcs'] ?? 85000);
            $prioritasBiaya = $item->design_data['biaya_prioritas'] ?? 0;
            
            $sizeBiaya = 0;
            if (isset($item->design_data['size_price_modifiers'])) {
                foreach (($item->design_data['ukuran'] ?? []) as $size => $q) {
                    $mod = $item->design_data['size_price_modifiers'][$size] ?? 0;
                    $sizeBiaya += ((int)$q * (int)$mod);
                }
            } else if (isset($item->design_data['size_price_modifier']) && isset($item->size)) {
                 $sizeBiaya = ((int)$qty * (int)($item->design_data['size_price_modifier'] ?? 0));
            }
            
            return ($qty * $basePrice) + $prioritasBiaya + $sizeBiaya;
        }

        $basePrice = $item->product->price ?? 0;
        $sizeModifier = 0;

        if ($item->product && $item->product->category) {
            $schema = $item->product->category->attributes_schema;
            $schemaArray = is_string($schema) ? json_decode($schema, true) : $schema;
            if (is_array($schemaArray)) {
                foreach ($schemaArray as $attr) {
                    if (isset($attr['system_tag']) && $attr['system_tag'] === 'is_size_type' && !empty($attr['options'])) {
                        foreach ($attr['options'] as $opt) {
                            if ($opt['value'] === $item->size && isset($opt['price_modifier'])) {
                                $sizeModifier = (int) $opt['price_modifier'];
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        return $item->qty * ($basePrice + $sizeModifier);
    }
}
