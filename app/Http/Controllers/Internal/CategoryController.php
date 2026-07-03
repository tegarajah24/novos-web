<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
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

        return view('internal.kelola-kategori', compact('categories'));
    }

    public function getData()
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn($cat) => [
                'id'             => $cat->id,
                'name'           => $cat->name,
                'products_count' => $cat->products_count,
            ]);

        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'success'  => true,
            'message'  => 'Kategori berhasil ditambahkan',
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

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
