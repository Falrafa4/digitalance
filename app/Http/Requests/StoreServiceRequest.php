<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            'category_id' => 'required|exists:service_categories,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'price_min' => 'required|numeric',
            'price_max' => 'required|numeric|gte:price_min',
            'delivery_time' => 'required|integer'
        ];
    }
}
