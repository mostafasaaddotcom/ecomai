<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     *
     * Get a product by ID. Admin tokens can access any product.
     * Regular tokens can only access products belonging to the authenticated user.
     */
    public function show(Product $product): ProductResource|JsonResponse
    {
        // Admin tokens can access any product
        if (!auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified product.
     *
     * Update a product's AI description and/or store link URL. Admin tokens can update any product.
     * Regular tokens can only update products belonging to the authenticated user.
     */
    public function update(Request $request, Product $product): ProductResource|JsonResponse
    {
        // Admin tokens can update any product
        if (!auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'description_ai' => ['nullable', 'string'],
            'store_link_url' => ['nullable', 'url'],
        ]);

        // Update the product with validated fields
        $product->update($validated);

        return new ProductResource($product);
    }
}
