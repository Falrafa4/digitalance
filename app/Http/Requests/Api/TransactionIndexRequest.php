<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TransactionIndexRequest extends FormRequest
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
            'status' => ['nullable', 'in:all,Paid,Pending,Failed,Refund,paid,pending,failed,refund'],
            'type' => ['nullable', 'in:DP,Full,Refund'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
