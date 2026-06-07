<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AdminOrderStoreRequest extends FormRequest
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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'brief' => ['required', 'string'],
            'status' => ['nullable', 'in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled'],
            'agreed_price' => ['nullable', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date'],
        ];
    }
}
