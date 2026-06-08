<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'client_id' => ['sometimes', 'required', 'integer', 'exists:clients,id'],
            'service_id' => ['sometimes', 'required', 'integer', 'exists:services,id'],
            'brief' => ['sometimes', 'required', 'string'],
            'status' => ['sometimes', 'required', 'in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled'],
            'agreed_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'deadline' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
