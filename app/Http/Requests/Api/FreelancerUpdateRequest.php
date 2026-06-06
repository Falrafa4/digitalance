<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FreelancerUpdateRequest extends FormRequest
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
        $freelancer = $this->route('freelancer');
        $studentId = is_object($freelancer) ? $freelancer->student_id : null;

        return [
            'bio' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'required', 'in:Pending,Approved,Suspended,Rejected'],
            'reject_reason' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('skomda_students', 'email')->ignore($studentId)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'profile_photo' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }
}
