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
            'team_name'      => 'nullable|string|max:255',
            'no_punggung'    => 'nullable|integer',
            'detail_sponsor' => 'nullable|string|max:255',
            'kerah'          => 'required|string|max:100',
            'bahan'          => 'required|string|max:100',
            'jenis_potongan' => 'required|string|in:REGULER,SLIMFIT CEWE,OVERSIZE,TUNIK,SLIM FIT UNISEX',
            'lengan_jahitan' => 'required|string|in:REGULER OVERDECK,REGULER PAKAI MANSET,RAGLAN A OVERDECK,RAGLAN A PAKAI MANSET,RAGLAN B OVERDECK,RAGLAN B PAKAI MANSET',
            'catatan'        => 'nullable|string|max:2000',
            'ukuran'         => 'nullable|array',
            'ukuran.*'       => 'integer|min:0',
            'total_qty'      => 'nullable|integer|min:1',
            'prioritas'      => 'nullable|string|in:normal,express,super_express',
            'pembayaran'     => 'nullable|string|max:50',
            'warna_utama'    => 'nullable|string|max:7',
            'warna_sekunder' => 'nullable|string|max:7',
            'logo'           => 'nullable|file|mimes:jpg,jpeg,png,ai,eps,psd|max:5120',
            'design_files'   => 'nullable|array',
            'design_files.*' => 'file|mimes:jpg,jpeg,png,pdf,ai,eps,psd,zip,rar|max:20480',
            'address_id'     => 'nullable|exists:customer_addresses,id,user_id,' . $this->user()?->id,
        ];
    }
}
