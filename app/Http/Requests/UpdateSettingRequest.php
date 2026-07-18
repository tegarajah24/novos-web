<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'      => 'required|string|max:255',
            'company_phone'     => 'nullable|string|max:50',
            'company_email'     => 'nullable|email|max:255',
            'company_address'   => 'nullable|string|max:1000',
            'company_instagram' => 'nullable|string|max:255',
            'hours_weekday'     => 'nullable|string|max:100',
            'hours_saturday'    => 'nullable|string|max:100',
            'hours_sunday'      => 'nullable|string|max:100',
            'prioritas_normal_estimasi'        => 'nullable|string|max:255',
            'prioritas_normal_biaya'           => 'nullable|numeric|min:0',
            'prioritas_normal_status'          => 'nullable|string|in:active,inactive',
            'prioritas_express_estimasi'       => 'nullable|string|max:255',
            'prioritas_express_biaya'          => 'nullable|numeric|min:0',
            'prioritas_express_status'         => 'nullable|string|in:active,inactive',
            'prioritas_super_express_estimasi' => 'nullable|string|max:255',
            'prioritas_super_express_biaya'    => 'nullable|numeric|min:0',
            'prioritas_super_express_status'   => 'nullable|string|in:active,inactive',
            'about_story'  => 'nullable|string|max:5000',
            'about_visi'   => 'nullable|string|max:1000',
            'about_misi'   => 'nullable|array',
            'about_misi.*' => 'nullable|string|max:255',
            'hero_beranda_bg'  => 'nullable|string|max:255',
            'hero_tentang_bg'  => 'nullable|string|max:255',
            'hero_katalog_bg'  => 'nullable|string|max:255',
            'hero_jersey_depan'    => 'nullable|string|max:255',
            'hero_jersey_belakang' => 'nullable|string|max:255',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.bank_name' => 'nullable|string|max:100',
            'bank_accounts.*.account_name' => 'nullable|string|max:255',
            'bank_accounts.*.account_number' => 'nullable|string|max:100',
        ];
    }
}
