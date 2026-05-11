<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkomdaStudentRequest extends FormRequest
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
        $id = $this->route('skomda_student');
        if (is_object($id)) $id = $id->id;

        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:skomda_students,email,' . $id,
            'nis'   => 'required|string|max:9|unique:skomda_students,nis,' . $id,
            'class' => 'required|string|max:255',
            'major' => 'required|in:SIJA,TJAT',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
