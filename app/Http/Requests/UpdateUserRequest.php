<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|string|in:Super Admin,Manager,Admin,Design,Produksi',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }
}
