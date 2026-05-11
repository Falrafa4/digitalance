<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminProfileRequest extends FormRequest
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
        $adminIdFromRoute = $this->route('administrator') ? $this->route('administrator')->id : null;
        $authId = auth()->guard('administrator')->id();
        $id = $adminIdFromRoute ?? $authId;
        $isSelf = $authId === (int)$id;
        
        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:administrators,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'current_password' => $isSelf ? 'required_with:password' : 'nullable'
        ];
    }
}
