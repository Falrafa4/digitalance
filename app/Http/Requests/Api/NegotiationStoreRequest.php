<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class NegotiationStoreRequest extends FormRequest
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
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'message' => ['nullable', 'required_without:reason', 'string', 'max:2000'],
            'reason' => ['nullable', 'required_with:new_price', 'string', 'max:1000'],
            'new_price' => ['nullable', 'integer', 'min:1000'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
