<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFreelancerRequest extends FormRequest
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
        $isFreelancer = auth('freelancer')->check();

        return [
            'bio' => 'nullable|string',
            'status' => $isFreelancer ? 'nullable|in:Pending,Approved,Suspended,Rejected' : 'required|in:Pending,Approved,Suspended,Rejected',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6', // Tambahkan antisipasi update password langsung
        ];
    }
}