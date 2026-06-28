<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name'     => 'required|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'required|string|max:30',
        ];
    }
}
