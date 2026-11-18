<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $ingredients = Ingredient::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where(
                    'is_active',
                    $request->boolean('is_active')
                );
            })
            ->when($request->boolean('low_stock'), function ($query) {
                $query->whereColumn(
                    'current_stock',
                    '<=',
                    'minimum_stock'
                );
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Data bahan baku berhasil diambil.',
            'data' => $ingredients,
        ]);
    }

public function store(StoreIngredientRequest $request)
{
    $ingredient = Ingredient::create([
        ...$request->validated(),
        'current_stock' => 0,
        'average_cost' => 0,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Bahan baku berhasil ditambahkan.',
        'data' => $ingredient,
    ], 201);
}

    public function show(Ingredient $ingredient)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail bahan baku berhasil diambil.',
            'data' => $ingredient,
        ]);
    }

    public function update(
        UpdateIngredientRequest $request,
        Ingredient $ingredient
    ) {
        $ingredient->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil diperbarui.',
            'data' => $ingredient->fresh(),
        ]);
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil dihapus.',
        ]);
    }
}