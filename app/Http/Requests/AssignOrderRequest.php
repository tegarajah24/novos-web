<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->hasFullAccess('orders');
    }

    public function rules(): array
    {
        return [
            'assignee_id' => 'nullable|exists:users,id',
        ];
    }
}
