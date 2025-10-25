<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAnalysisResource;
use App\Models\Product;
use App\Models\ProductAnalysis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductAnalysisController extends Controller
{
    /**
     * Get the product analysis for a specific product.
     */
    public function show(Product $product): ProductAnalysisResource|JsonResponse
    {
        // Admin tokens can access any product, regular tokens only their own
        if (! auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        // Get the analysis
        $analysis = $product->analysis;

        if (!$analysis) {
            return response()->json([
                'message' => 'No analysis found for this product.',
            ], 404);
        }

        return new ProductAnalysisResource($analysis);
    }

    /**
     * Update the specified product analysis.
     *
     * Update a product analysis fields. Admin tokens can update any analysis.
     * Regular tokens can only update analyses belonging to the authenticated user.
     */
    public function update(Request $request, ProductAnalysis $productAnalysis): ProductAnalysisResource|JsonResponse
    {
        // Admin tokens can update any product analysis
        if (! auth()->user()->canAccessResource($productAnalysis)) {
            return response()->json([
                'message' => 'Forbidden. This product analysis does not belong to you.',
            ], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'core_function_and_use' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'benefits' => ['nullable', 'array'],
            'problems' => ['nullable', 'array'],
            'goals' => ['nullable', 'array'],
            'emotions' => ['nullable', 'array'],
            'objections' => ['nullable', 'array'],
            'faqs' => ['nullable', 'array'],
        ]);

        // Update the product analysis
        $productAnalysis->update($validated);

        return new ProductAnalysisResource($productAnalysis);
    }
}
