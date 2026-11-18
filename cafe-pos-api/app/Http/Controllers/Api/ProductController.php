<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where(
                    'is_active',
                    $request->boolean('is_active')
                );
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil.',
            'data' => $products,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'sku' => $this->generateSku(),
            'barcode' => $validated['barcode'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
            'image_path' => $imagePath,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product->load('category'),
        ], 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('category'),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $data = [
            'category_id' => $validated['category_id'],
            'barcode' => $validated['barcode'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
        ];

        if (array_key_exists('is_active', $validated)) {
            $data['is_active'] = $validated['is_active'];
        }

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')
                ->store('products', 'public');

            $oldImage = $product->image_path;

            $data['image_path'] = $newImage;

            $product->update($data);

            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        } else {
            $product->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product->fresh()->load('category'),
        ]);
    }

    public function destroy(Product $product)
    {
        // Soft delete: data produk tetap tersimpan.
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ]);
    }

    private function generateSku(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(Str::random(8));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}