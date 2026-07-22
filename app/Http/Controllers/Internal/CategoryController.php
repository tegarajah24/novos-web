<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent'])->withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn($cat) => [
                'id'                         => $cat->id,
                'name'                       => $cat->name,
                'icon'                       => $cat->icon,
                'description'                => $cat->description,
                'attributes_schema'           => $cat->attributes_schema ?? [],
                'effective_attributes_schema' => $cat->getEffectiveAttributesSchema(),
                'form_config'                => $cat->form_config,
                'base_price'                 => $cat->base_price,
                'products_count'             => $cat->products_count,
                'parent_id'                  => $cat->parent_id,
                'parent_name'                => $cat->parent ? $cat->parent->name : null,
            ]);

        return view('internal.kelola-kategori', compact('categories'));
    }

    public function getData()
    {
        $categories = Category::with(['parent'])->withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn($cat) => [
                'id'                         => $cat->id,
                'name'                       => $cat->name,
                'icon'                       => $cat->icon,
                'description'                => $cat->description,
                'attributes_schema'           => $cat->attributes_schema ?? [],
                'effective_attributes_schema' => $cat->getEffectiveAttributesSchema(),
                'form_config'                => $cat->form_config,
                'base_price'                 => $cat->base_price,
                'products_count'             => $cat->products_count,
                'parent_id'                  => $cat->parent_id,
                'parent_name'                => $cat->parent ? $cat->parent->name : null,
            ]);

        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('categories', 'public');
            $data['icon'] = $path;
        }

        $category = Category::create($data);

        return response()->json([
            'success'  => true,
            'message'  => 'Kategori berhasil ditambahkan',
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        if ($request->hasFile('icon')) {
            if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                Storage::disk('public')->delete($category->icon);
            }
            $path = $request->file('icon')->store('categories', 'public');
            $data['icon'] = $path;
        } else {
            unset($data['icon']);
        }

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
