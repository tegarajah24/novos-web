<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action'       => 'required|in:proses_printing,selesai_printing,proses_jahit,selesai_jahit,proses_qc,selesai_qc,revisi_qc',
            'notes'        => 'nullable|string|max:2000',
            'target_stage' => 'nullable|required_if:action,revisi_qc|in:jahit,printing',
        ];
    }
}
