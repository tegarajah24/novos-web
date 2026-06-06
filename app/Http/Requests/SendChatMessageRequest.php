<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required_without:file|string|max:5000',
            'file' => 'required_without:message|file|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required_without' => 'Pesan atau file harus diisi',
            'file.required_without' => 'Pesan atau file harus diisi',
            'file.max' => 'Ukuran file maksimal 20 MB',
        ];
    }
}
