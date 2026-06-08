<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResultStoreRequest extends FormRequest
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
            'result_mode' => ['required', 'in:file,link'],
            'file' => ['required_if:result_mode,file', 'file', 'mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png', 'max:51200'],
            'result_link' => ['required_if:result_mode,link', 'url', 'max:2048'],
            'note' => ['nullable', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:100'],
        ];
    }
}
