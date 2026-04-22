<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFreelancerRequest extends FormRequest
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
            'student_id' => 'required|exists:skomda_students,id',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'Student ID wajib dipilih.',
            'student_id.exists' => 'Student ID tidak ditemukan. Pilih dari dropdown siswa.',
        ];
    }
}
