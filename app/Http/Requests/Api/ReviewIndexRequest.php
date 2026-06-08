<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ReviewIndexRequest extends FormRequest
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
            'rating' => ['nullable', 'in:1,2,3,4,5,low'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
