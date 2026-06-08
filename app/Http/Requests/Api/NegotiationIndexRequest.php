<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class NegotiationIndexRequest extends FormRequest
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
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'status' => ['nullable', 'in:Pending,Accepted,Rejected'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
