<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:categories,id',
            'price'              => 'required|numeric|min:0',
            'description'        => 'nullable|string|max:5000',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'existing_images'    => 'nullable|array',
            'existing_images.*'  => 'string',
            // Kolom lama (tetap nullable untuk backward compat)
            'kerah'              => 'nullable|string|max:100',
            'bahan'              => 'nullable|string|max:100',
            'jenis_potongan'     => 'nullable|string|max:100',
            'lengan_jahitan'     => 'nullable|string|max:100',
            // Kolom baru: atribut dinamis JSON
            'product_attributes' => 'nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('product_attributes') && is_string($this->product_attributes)) {
            $decoded = json_decode($this->product_attributes, true);
            if (is_array($decoded)) {
                $this->merge(['product_attributes' => $decoded]);
            }
        }
    }
}
