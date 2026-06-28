<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMicroBreakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checklist' => 'required|array|size:8',
            'checklist.*' => 'required|integer|in:0,1',
            'eval' => 'required|array|size:3',
            'eval.stres' => 'required|in:lebih_baik,sama,lebih_buruk',
            'eval.fokus' => 'required|in:lebih_baik,sama,lebih_buruk',
            'eval.kenyamanan' => 'required|in:lebih_baik,sama,lebih_buruk',
            'catatan_membantu' => 'nullable|string|max:500',
            'catatan_kendala' => 'nullable|string|max:500',
        ];
    }
}
