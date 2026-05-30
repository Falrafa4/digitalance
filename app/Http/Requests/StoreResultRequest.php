<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'result_mode' => 'required|in:file,link',
            'file' => 'required_if:result_mode,file|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:51200',
            'result_link' => 'required_if:result_mode,link|url|max:2048',
            'note' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:100',
        ];
    }
}
