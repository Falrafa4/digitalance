<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
        $role = $this->user()?->getRole();

        $rules = [
            'brief' => ['required', 'string'],
            'deadline' => ['nullable', 'date'],
        ];

        if ($role === 'client') {
            return $rules + [
                'service_id' => ['required', 'integer', 'exists:services,id'],
                'attachments' => ['nullable', 'array', 'max:10'],
                'attachments.*' => ['file', 'max:51200'],
            ];
        }

        return $rules + [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'status' => ['nullable', 'in:Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled'],
            'agreed_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
