<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFreelancerRequest extends FormRequest
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
            'password' => 'required|string|min:6',
            'bio' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }
}
