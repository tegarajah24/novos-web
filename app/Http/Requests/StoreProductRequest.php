<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'price'           => 'required|numeric|min:0',
            'description'     => 'nullable|string|max:5000',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kerah'           => 'nullable|string|max:100',
            'bahan'           => 'nullable|string|max:100',
            'jenis_potongan'  => 'nullable|string|max:100',
            'lengan_jahitan'  => 'nullable|string|max:100',
        ];
    }
}
