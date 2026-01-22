<?php

namespace App\Presentation\Requests\Price;

use Illuminate\Foundation\Http\FormRequest;

class PriceListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'active_only' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'active_only.boolean' => 'active_onlyはboolean型である必要があります',
        ];
    }
}