<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientProfileRequest extends FormRequest
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
        $client = $this->route('client') ?? $this->user('client');
        $clientId = is_object($client) ? $client->id : $client;

        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:clients,email'.($clientId ? ','.$clientId : ''),
            'phone' => 'required|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }
}
