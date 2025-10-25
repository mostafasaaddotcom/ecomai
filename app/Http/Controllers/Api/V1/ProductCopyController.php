<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCopyResource;
use App\Models\Product;
use App\Models\ProductCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCopyController extends Controller
{
    /**
     * Display a listing of the product copies.
     */
    public function index(Product $product)
    {
        // Admin tokens can access any product, regular tokens only their own
        if (! auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        $copies = $product->copies()->latest()->get();

        return ProductCopyResource::collection($copies);
    }

    /**
     * Store a newly created product copy.
     */
    public function store(Request $request, Product $product)
    {
        // Admin tokens can access any product, regular tokens only their own
        if (! auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        $validated = $request->validate([
            'angle' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:ugc,expert,background_voice'],
            'formula' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:255'],
            'tone' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'voice_url_link' => ['nullable', 'string', 'url'],
        ]);

        $copy = $product->copies()->create([
            'user_id' => $product->user_id,
            ...$validated,
        ]);

        return new ProductCopyResource($copy);
    }

    /**
     * Update the specified product copy.
     */
    public function update(Request $request, ProductCopy $productCopy)
    {
        // Admin tokens can update any copy, regular tokens only their own
        if (! auth()->user()->canAccessResource($productCopy)) {
            return response()->json([
                'message' => 'Forbidden. This product copy does not belong to you.',
            ], 403);
        }

        $validated = $request->validate([
            'angle' => ['nullable', 'string', 'max:255'],
            'tone' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'voice_url_link' => ['nullable', 'string', 'url'],
        ]);

        $productCopy->update($validated);

        return new ProductCopyResource($productCopy);
    }

    /**
     * Remove the specified product copy.
     */
    public function destroy(ProductCopy $productCopy)
    {
        // Admin tokens can delete any copy, regular tokens only their own
        if (! auth()->user()->canAccessResource($productCopy)) {
            return response()->json([
                'message' => 'Forbidden. This product copy does not belong to you.',
            ], 403);
        }

        $productCopy->delete();

        return response()->json(['message' => 'Product copy deleted successfully'], 200);
    }
}
