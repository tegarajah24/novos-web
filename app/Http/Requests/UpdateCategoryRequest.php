<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $categoryId = $category instanceof Category ? $category->id : $category;

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
}
