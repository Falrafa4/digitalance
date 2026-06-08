<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LokerApplicationStoreRequest extends FormRequest
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
            'proposal' => ['required', 'string', 'min:20'],
            'proposed_price' => ['nullable', 'numeric', 'min:1000'],
        ];
    }
}
