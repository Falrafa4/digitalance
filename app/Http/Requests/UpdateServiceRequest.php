<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'freelancer_id' => 'required|exists:freelancers,id',
            'title' => 'required|string',
            'delivery_time' => 'required|integer',
            'price_min' => 'required|numeric',
            'price_max' => 'required|numeric|gte:price_min',
            'description' => 'required|string'
        ];
    }
}
