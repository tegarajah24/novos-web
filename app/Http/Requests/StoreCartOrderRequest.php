<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_item_ids'   => 'required|array',
            'cart_item_ids.*' => 'exists:carts,id',
            'prioritas'       => 'nullable|string|in:normal,express,super_express',
            'pembayaran'      => 'nullable|string|max:50',
            'address_id'      => 'nullable|exists:customer_addresses,id,user_id,' . $this->user()?->id,
        ];
    }
}
