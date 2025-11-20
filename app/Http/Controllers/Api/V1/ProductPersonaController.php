<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductPersonaResource;
use App\Models\Product;
use App\Models\ProductPersona;
use Illuminate\Http\Request;

class ProductPersonaController extends Controller
{
    /**
     * Store a newly created product persona.
     */
    public function store(Request $request, Product $product)
    {
        // Admin tokens can access any product, regular tokens only their own
        if (! auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'main_problem' => ['nullable', 'string'],
        ]);

        // Create persona
        $persona = ProductPersona::create($validated);

        return new ProductPersonaResource($persona);
    }
}
