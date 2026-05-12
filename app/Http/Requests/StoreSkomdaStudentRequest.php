<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkomdaStudentRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:skomda_students,email',
            'nis' => 'required|string|max:9|unique:skomda_students,nis',
            'class' => 'required|string|max:255',
            'major' => 'required|in:SIJA,TJAT',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
