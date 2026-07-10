<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_name'        => 'nullable|string|max:255',
            'nama_artikel'     => 'nullable|string|max:255',
            'nama_pemesan'     => 'nullable|string|max:255',
            'detail_sponsor'   => 'nullable|string|max:255',
            // Kolom lama jersey — dibuat nullable karena sekarang digantikan customizations
            'kerah'            => 'nullable|string|max:100',
            'bahan'            => 'nullable|string|max:100',
            'jenis_potongan'   => 'nullable|string|max:100',
            'lengan_jahitan'   => 'nullable|string|max:100',
            // Field baru: atribut dinamis dari category.attributes_schema
            'customizations'   => 'nullable',
            'catatan'          => 'nullable|string|max:5000',
            'total_qty'        => 'nullable|integer|min:1',
            'prioritas'        => 'nullable|string|in:normal,express,super_express',
            'warna_utama'      => 'nullable|string|max:7',
            'warna_sekunder'   => 'nullable|string|max:7',
            'logo'             => 'nullable|file|mimes:jpg,jpeg,png,ai,eps,psd|max:5120',
            'logo_files'       => 'nullable|array',
            'logo_files.*'     => 'file|mimes:jpg,jpeg,png,ai,eps,psd|max:5120',
            'design_files'     => 'nullable|array',
            'design_files.*'   => 'file|mimes:jpg,jpeg,png,pdf,ai,eps,psd,zip,rar|max:20480',
            'address_id'       => 'nullable|exists:customer_addresses,id,user_id,' . $this->user()?->id,
            'phone'            => 'required|string|max:20',
        ];
    }
}
