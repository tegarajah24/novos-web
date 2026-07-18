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
        $schema = $request->input('attributes_schema');

        // Normalize: coerce empty/missing price_modifier to 0 BEFORE validation
        if (is_array($schema)) {
            foreach ($schema as &$attr) {
                if (isset($attr['options']) && is_array($attr['options'])) {
                    foreach ($attr['options'] as &$opt) {
                        if (!isset($opt['price_modifier']) || $opt['price_modifier'] === '' || $opt['price_modifier'] === null) {
                            $opt['price_modifier'] = 0;
                        }
                    }
                    unset($opt);
                }
            }
            unset($attr);
            $request->merge(['attributes_schema' => $schema]);
        }

        $request->validate([
            'attributes_schema'                       => 'required|array',
            'attributes_schema.*.id'                  => 'required|string|max:100',
            'attributes_schema.*.name'                => 'required|string|max:200',
            'attributes_schema.*.type'                => 'required|in:select,radio,text',
            'attributes_schema.*.required'            => 'boolean',
            'attributes_schema.*.options'                    => 'nullable|array',
            'attributes_schema.*.options.*.value'            => 'required|string|max:200',
            'attributes_schema.*.options.*.price_modifier'   => 'nullable|numeric',
            'attributes_schema.*.options.*.sleeve'           => 'nullable|in:long,short',
            'attributes_schema.*.system_tag'                 => 'nullable|in:is_fabric_type,is_collar_type,is_cut_type,is_sleeve_joint_type,is_sleeve_type',
            'attributes_schema.*.depends_on'                 => 'nullable|array',
            'attributes_schema.*.depends_on.attribute_id'    => 'nullable|string',
            'attributes_schema.*.depends_on.value'           => 'nullable|string',
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
