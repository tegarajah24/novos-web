<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryAttributeController extends Controller
{
    /**
     * Ambil schema atribut untuk satu kategori.
     */
    public function getSchema(Category $category)
    {
        return response()->json([
            'success'           => true,
            'category_id'       => $category->id,
            'category_name'     => $category->name,
            'attributes_schema' => $category->attributes_schema ?? [],
        ]);
    }

    /**
     * Simpan / update schema atribut untuk satu kategori.
     * Schema dikirim sebagai JSON array dari frontend.
     */
    public function updateSchema(Request $request, Category $category)
    {
        $request->validate([
            'attributes_schema'                       => 'required|array',
            'attributes_schema.*.id'                  => 'required|string|max:100',
            'attributes_schema.*.name'                => 'required|string|max:200',
            'attributes_schema.*.type'                => 'required|in:select,radio,text',
            'attributes_schema.*.required'            => 'boolean',
            'attributes_schema.*.options'             => 'nullable|array',
            'attributes_schema.*.options.*.value'     => 'required|string|max:200',
            'attributes_schema.*.depends_on'          => 'nullable|array',
            'attributes_schema.*.depends_on.attribute_id' => 'nullable|string',
            'attributes_schema.*.depends_on.value'    => 'nullable|string',
        ]);

        $schema = $request->input('attributes_schema');

        // Pastikan setiap atribut type 'select'/'radio' punya minimal 1 opsi
        foreach ($schema as $attr) {
            if (in_array($attr['type'], ['select', 'radio'])) {
                if (empty($attr['options'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Atribut \"{$attr['name']}\" bertipe {$attr['type']} wajib memiliki minimal 1 opsi.",
                    ], 422);
                }
            }
        }

        $category->update(['attributes_schema' => $schema]);

        return response()->json([
            'success'           => true,
            'message'           => 'Schema atribut berhasil disimpan.',
            'attributes_schema' => $category->fresh()->attributes_schema ?? [],
        ]);
    }
}
