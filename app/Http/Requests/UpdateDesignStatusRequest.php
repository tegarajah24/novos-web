<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:siap_cetak',
            'files'  => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,zip,rar,ai,eps,psd|max:20480',
        ];
    }
}
