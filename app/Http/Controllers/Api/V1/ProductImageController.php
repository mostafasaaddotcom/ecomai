<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductImageController extends Controller
{
    /**
     * Handle webhook callback from AI image provider.
     */
    public function webhook(Request $request)
    {
        try {
            // Validate incoming webhook data
            $validated = $request->validate([
                'image_id' => 'nullable|exists:product_images,id',
                'reference_id' => 'required|string',
                'status' => 'required|in:prompt_generated,image_generating,completed,failed',
                'image_url' => 'nullable|string',
                'prompt' => 'nullable|string',
                'aspect_ratio' => 'nullable|string',
                'error_message' => 'nullable|string',
            ]);

            // Find the image by reference_id or image_id
            $image = null;
            if (isset($validated['image_id'])) {
                $image = ProductImage::find($validated['image_id']);
            } elseif (isset($validated['reference_id'])) {
                $image = ProductImage::where('reference_id', $validated['reference_id'])->first();
            }

            if (!$image) {
                Log::warning('ProductImage not found for webhook', [
                    'image_id' => $validated['image_id'] ?? null,
                    'reference_id' => $validated['reference_id'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                ], 404);
            }

            // Update image with webhook data
            $updateData = [
                'status' => $validated['status'],
            ];

            if (isset($validated['image_url'])) {
                $updateData['image_url'] = $validated['image_url'];
            }

            if (isset($validated['prompt'])) {
                $updateData['prompt'] = $validated['prompt'];
            }

            if (isset($validated['aspect_ratio'])) {
                $updateData['aspect_ratio'] = $validated['aspect_ratio'];
            }

            if (isset($validated['reference_id']) && !$image->reference_id) {
                $updateData['reference_id'] = $validated['reference_id'];
            }

            $image->update($updateData);

            Log::info('ProductImage updated via webhook', [
                'image_id' => $image->id,
                'status' => $validated['status'],
                'reference_id' => $validated['reference_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully',
                'data' => [
                    'image_id' => $image->id,
                    'status' => $image->status,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Webhook validation failed', [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Display a listing of the product images.
     */
    public function index(Product $product)
    {
        // Admin tokens can access any product, regular tokens only their own
        if (!auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        $images = $product->images()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $images,
        ], 200);
    }

    /**
     * Create a new product image.
     */
    public function store(Request $request, Product $product)
    {
        // Admin tokens can access any product, regular tokens only their own
        if (!auth()->user()->canAccessResource($product)) {
            return response()->json([
                'message' => 'Forbidden. This product does not belong to you.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'type' => 'required|in:product_only,lifestyle,ugc_scene,expert,other',
                'reference_id' => 'nullable|string',
                'prompt' => 'nullable|string',
                'status' => 'required|in:prompt_generated,image_generating,completed,failed',
                'image_url' => 'nullable|string',
                'aspect_ratio' => 'nullable|string',
            ]);

            $image = $product->images()->create([
                'user_id' => $product->user_id,
                'type' => $validated['type'],
                'reference_id' => $validated['reference_id'] ?? null,
                'prompt' => $validated['prompt'] ?? null,
                'status' => $validated['status'],
                'image_url' => $validated['image_url'] ?? null,
                'aspect_ratio' => $validated['aspect_ratio'] ?? null,
                'is_ai_generated' => true,
            ]);

            Log::info('Product image created', [
                'image_id' => $image->id,
                'product_id' => $product->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image created successfully',
                'data' => $image,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Image creation failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create image',
            ], 500);
        }
    }

    /**
     * Get a product image by reference ID.
     */
    public function getByReferenceId(string $referenceId)
    {
        $image = ProductImage::where('reference_id', $referenceId)->first();

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Product image not found',
            ], 404);
        }

        // Admin tokens can access any product image, regular tokens only their own
        if (!auth()->user()->canAccessResource($image)) {
            return response()->json([
                'message' => 'Forbidden. This product image does not belong to you.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $image,
        ], 200);
    }

    /**
     * Update the specified product image.
     *
     * Update a product image's metadata and URL. Admin tokens can update any image.
     * Regular tokens can only update images belonging to the authenticated user.
     */
    public function update(Request $request, ProductImage $productImage)
    {
        // Admin tokens can update any product image, regular tokens only their own
        if (!auth()->user()->canAccessResource($productImage)) {
            return response()->json([
                'message' => 'Forbidden. This product image does not belong to you.',
            ], 403);
        }

        try {
            // Validate the request
            $validated = $request->validate([
                'type' => ['nullable', 'in:product_only,lifestyle,ugc_scene,expert,other'],
                'prompt' => ['nullable', 'string'],
                'image_url' => ['nullable', 'string'],
                'aspect_ratio' => ['nullable', 'string'],
                'status' => ['nullable', 'in:prompt_generated,image_generating,completed,failed'],
                'reference_id' => ['nullable', 'string'],
            ]);

            // Update the product image
            $productImage->update($validated);

            Log::info('Product image updated', [
                'image_id' => $productImage->id,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully',
                'data' => $productImage,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Image update validation failed', [
                'errors' => $e->errors(),
                'image_id' => $productImage->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Image update failed', [
                'error' => $e->getMessage(),
                'image_id' => $productImage->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update image',
            ], 500);
        }
    }

    /**
     * Download the specified product image.
     */
    public function download(ProductImage $productImage)
    {
        // Check if user can access this image
        if (!auth()->user()->canAccessResource($productImage)) {
            abort(403, 'Forbidden. This product image does not belong to you.');
        }

        // Check if image is completed and has a URL
        if ($productImage->status !== 'completed' || !$productImage->image_url) {
            abort(404, 'Image not available for download.');
        }

        try {
            // Fetch the image content from the external URL
            $imageContent = file_get_contents($productImage->image_url);

            if ($imageContent === false) {
                abort(500, 'Failed to fetch image from storage.');
            }

            // Get the file extension from the URL or default to jpg
            $extension = pathinfo(parse_url($productImage->image_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';

            // Create a filename: product_{id}_{type}_{timestamp}.{ext}
            $filename = sprintf(
                'product_%d_%s_%s.%s',
                $productImage->product_id,
                $productImage->type,
                $productImage->id,
                $extension
            );

            // Determine MIME type based on extension
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

            // Return the image as a download
            return response($imageContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            Log::error('Image download failed', [
                'error' => $e->getMessage(),
                'image_id' => $productImage->id,
                'image_url' => $productImage->image_url,
            ]);

            abort(500, 'Failed to download image.');
        }
    }
}
