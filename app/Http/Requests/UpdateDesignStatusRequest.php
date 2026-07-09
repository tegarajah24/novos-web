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
            'status'                => 'required|in:menunggu_spk',
            'mockup_files'          => 'nullable|array',
            'mockup_files.*'        => 'file|mimes:jpg,jpeg,png,pdf,zip,rar,ai,eps,psd|max:20480',
            'detail_depan_files'    => 'nullable|array',
            'detail_depan_files.*'  => 'file|mimes:jpg,jpeg,png,pdf,zip,rar,ai,eps,psd|max:20480',
            'nama_punggung_files'   => 'nullable|array',
            'nama_punggung_files.*' => 'file|mimes:jpg,jpeg,png,pdf,zip,rar,ai,eps,psd|max:20480',
            'detail_sponsor_files'  => 'nullable|array',
            'detail_sponsor_files.*' => 'file|mimes:jpg,jpeg,png,pdf,zip,rar,ai,eps,psd|max:20480',
        ];
    }
}
