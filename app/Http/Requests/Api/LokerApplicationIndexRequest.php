<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LokerApplicationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:Pending,Approved,Rejected'],
            'loker_id' => ['nullable', 'integer', 'exists:lokkers,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
