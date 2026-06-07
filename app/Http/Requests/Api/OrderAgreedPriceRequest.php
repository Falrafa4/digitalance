<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderAgreedPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('agreed_price')) {
            $this->merge([
                'agreed_price' => $this->sanitizeRupiahInput($this->input('agreed_price')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'agreed_price' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }

    private function sanitizeRupiahInput($value)
    {
        if ($value === null || is_numeric($value)) {
            return $value;
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9,\.\-]/u', '', $raw);

        if (strpos($clean, ',') !== false && preg_match('/,[0-9]{1,2}$/', $clean)) {
            $normalized = str_replace('.', '', $clean);
            $normalized = str_replace(',', '.', $normalized);

            return is_numeric($normalized) ? $normalized : null;
        }

        $normalized = str_replace(['.', ','], '', $clean);

        return is_numeric($normalized) ? $normalized : null;
    }
}
