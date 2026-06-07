<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'student_id' => [
                'required',
                Rule::exists('skomda_students', 'id')->where('is_registered', false),
                'unique:freelancers,student_id',
            ],
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'Student ID wajib dipilih.',
            'student_id.exists' => 'Student ID tidak ditemukan atau sudah terdaftar sebagai freelancer.',
            'student_id.unique' => 'Akun freelancer untuk siswa ini sudah terdaftar. Silakan login.',
        ];
    }
}
