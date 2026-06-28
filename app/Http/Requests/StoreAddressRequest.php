<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'province'       => 'required|string|max:255',
            'city'           => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'detail_address' => 'required|string|max:2000',
            'postal_code'    => 'required|string|max:10',
            'address_type'   => 'required|in:rumah,kantor',
        ];
    }
}
