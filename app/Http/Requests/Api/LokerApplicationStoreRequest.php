<?php

namespace App\Http\Requests\Api;

use App\Models\Loker;
use Illuminate\Foundation\Http\FormRequest;

class LokerApplicationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $proposedPriceRules = ['nullable', 'numeric', 'min:1000'];
        $loker = $this->route('loker');

        if ($loker instanceof Loker && $loker->budget_max !== null) {
            $proposedPriceRules[] = 'max:' . (float) $loker->budget_max;
        }

        return [
            'proposal' => ['required', 'string', 'min:20'],
            'proposed_price' => $proposedPriceRules,
        ];
    }

    public function messages(): array
    {
        return [
            'proposed_price.max' => 'Harga tawaran tidak boleh melebihi budget maksimum client.',
        ];
    }
}
