<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResultIndexRequest extends FormRequest
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
            'result_mode' => ['nullable', 'in:file,link'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
