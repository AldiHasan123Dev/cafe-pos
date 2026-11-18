<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ingredient = $this->route('ingredient');

        $ingredientId = $ingredient instanceof \App\Models\Ingredient
            ? $ingredient->id
            : $ingredient;

        return [
            'sku' => [
                'required',
                'string',
                'max:30',
                Rule::unique('ingredients', 'sku')->ignore($ingredientId),
            ],
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:30',
            'minimum_stock' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ];
    }
}