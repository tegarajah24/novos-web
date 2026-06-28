<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chat_id' => 'nullable|exists:chats,id',
            'message' => 'nullable|string|max:2000',
            'file'    => 'nullable|file|max:20480',
        ];
    }
}
