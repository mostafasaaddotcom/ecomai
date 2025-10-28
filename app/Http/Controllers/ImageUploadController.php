<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Upload main product image via AJAX
     */
    public function uploadMainImage(Request $request)
    {
        try {
            $validated = $request->validate([
                'main_image' => ['required', 'image', 'max:2048'], // 2MB Max
            ]);

            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('products', 'public');
                $imageUrl = Storage::url($imagePath);

                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'path' => $imagePath,
                    'url' => $imageUrl,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided',
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading main image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the image',
            ], 500);
        }
    }

    /**
     * Upload additional product images via AJAX
     */
    public function uploadProductImages(Request $request, Product $product)
    {
        try {
            // Check if user owns the product
            if ($product->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'images' => ['required', 'array'],
                'images.*' => ['required', 'image', 'max:10240'], // Max 10MB
                'upload_type' => ['required', 'string'],
            ]);

            $uploadedImages = [];

            foreach ($request->file('images') as $image) {
                // Store the image
                $path = $image->store('product-images', 'public');
                $url = Storage::url($path);

                // Create product image record
                $productImage = ProductImage::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'type' => $validated['upload_type'],
                    'image_url' => $url,
                    'is_ai_generated' => false,
                    'status' => 'completed',
                ]);

                $uploadedImages[] = [
                    'id' => $productImage->id,
                    'url' => $url,
                    'type' => $validated['upload_type'],
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'images' => $uploadedImages,
                'count' => count($uploadedImages),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading product images', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading images',
            ], 500);
        }
    }
}
