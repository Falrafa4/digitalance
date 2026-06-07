<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SkomdaStudentStoreRequest extends FormRequest
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
            'nis' => ['required', 'string', 'max:9', 'unique:skomda_students,nis'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:skomda_students,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'class' => ['required', 'string', 'max:255'],
            'major' => ['required', 'in:SIJA,TJAT'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }
}
