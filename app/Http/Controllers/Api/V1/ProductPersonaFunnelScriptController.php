<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductPersonaFunnelScriptResource;
use App\Models\Product;
use App\Models\ProductPersonaFunnelScript;
use Illuminate\Http\Request;

class ProductPersonaFunnelScriptController extends Controller
{
    /**
     * Store a newly created funnel script in storage.
     */
    public function store(Request $request, Product $product)
    {
        // Check if user can access this product (supports admin tokens)
        if (! auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'product_persona_id' => ['required', 'exists:product_personas,id'],
            'stage' => ['required', 'in:unaware,problem_aware,solution_aware,product_aware,most_aware'],
            'angle' => ['nullable', 'string', 'max:255'],
            'formula' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:255'],
            'tone' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'voice_link_url' => ['nullable', 'string', 'url'],
        ]);

        // Create the funnel script
        $funnelScript = ProductPersonaFunnelScript::create($validated);

        // Return the resource
        return new ProductPersonaFunnelScriptResource($funnelScript);
    }
}
