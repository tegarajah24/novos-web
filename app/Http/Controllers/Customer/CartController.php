<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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
            if ($item->design_data) {
                $qty = collect($item->design_data['ukuran'] ?? [])->sum(fn($v) => (int) $v);
                $basePrice = ($item->design_data['base_price_per_pcs'] ?? 85000);
                $prioritasBiaya = $item->design_data['biaya_prioritas'] ?? 0;
                return ($qty * $basePrice) + $prioritasBiaya;
            }
            return $item->qty * ($item->product->price ?? 0);
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

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string',
            'qty' => 'required|integer|min:1',
        ]);

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
            ]
        );

        $count = Cart::where('user_id', auth()->id())->sum('qty');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => $cart->load('product.category'),
            'count' => $count,
        ]);
    }

    public function storeDesign(Request $request): JsonResponse
    {
        $request->validate([
            'team_name' => 'nullable|string',
            'design_data' => 'required|array',
        ]);

        $designData = $request->design_data;
        $totalQty = collect($designData['ukuran'] ?? [])->sum(fn($v) => (int) $v);

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

    public function update(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $cart->update(['qty' => $request->qty]);

        $totalSelected = Cart::with('product')
            ->where('user_id', auth()->id())
            ->where('is_selected', true)
            ->get()
            ->sum(function ($item) {
                if ($item->design_data) {
                    $qty = collect($item->design_data['ukuran'] ?? [])->sum(fn($v) => (int) $v);
                    $basePrice = ($item->design_data['base_price_per_pcs'] ?? 85000);
                    $prioritasBiaya = $item->design_data['biaya_prioritas'] ?? 0;
                    return ($qty * $basePrice) + $prioritasBiaya;
                }
                return $item->qty * ($item->product->price ?? 0);
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
                if ($item->design_data) {
                    $qty = collect($item->design_data['ukuran'] ?? [])->sum(fn($v) => (int) $v);
                    $basePrice = ($item->design_data['base_price_per_pcs'] ?? 85000);
                    $prioritasBiaya = $item->design_data['biaya_prioritas'] ?? 0;
                    return ($qty * $basePrice) + $prioritasBiaya;
                }
                return $item->qty * ($item->product->price ?? 0);
            });

        return response()->json([
            'success' => true,
            'is_selected' => $cart->is_selected,
            'total_selected' => $totalSelected,
        ]);
    }
}
