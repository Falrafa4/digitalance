<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
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
            'status' => ['nullable', 'in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled'],
            'payout' => ['nullable', 'in:all,paid,pending'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
