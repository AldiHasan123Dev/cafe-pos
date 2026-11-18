<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

      public function rules(): array
    {
        return [
            'sku' => 'required|string|max:30|unique:ingredients,sku',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:30',
            'minimum_stock' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }
}