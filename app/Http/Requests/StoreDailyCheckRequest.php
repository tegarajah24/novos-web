<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => 'required|array|size:5',
            'answers.*' => 'required|integer|in:1,2,3',
            'need_help' => 'required|in:ya,tidak',
            'help_note' => 'nullable|string|max:500',
        ];
    }
}
