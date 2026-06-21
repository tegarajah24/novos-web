<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn($cat) => [
                'id'             => $cat->id,
                'name'           => $cat->name,
                'products_count' => $cat->products_count,
            ]);

        if (request()->wantsJson()) {
            return response()->json($categories);
        }

        return view('internal.kelola-kategori', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create($data);

        return response()->json([
            'success'  => true,
            'message'  => 'Kategori berhasil ditambahkan',
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($data);

        return response()->json([
            'success'  => true,
            'message'  => 'Kategori berhasil diperbarui',
            'category' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak bisa dihapus karena masih memiliki produk terkait.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
