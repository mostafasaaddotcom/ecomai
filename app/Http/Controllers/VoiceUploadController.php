<?php

namespace App\Http\Controllers;

use App\Models\ProductCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VoiceUploadController extends Controller
{
    /**
     * Upload voice file for a product copy via AJAX
     */
    public function uploadVoice(Request $request, ProductCopy $copy)
    {
        try {
            // Check if user owns the copy
            if ($copy->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'voice_file' => [
                    'required',
                    'file',
                    'mimes:mp3,mpga,m4a',
                    'max:25600', // Max 25MB
                ],
            ]);

            if ($request->hasFile('voice_file')) {
                $file = $request->file('voice_file');

                // Delete old voice file if it exists and is stored locally
                if ($copy->voice_url_link && str_contains($copy->voice_url_link, '/storage/product-voices/')) {
                    $oldPath = str_replace('/storage/', '', parse_url($copy->voice_url_link, PHP_URL_PATH));
                    Storage::disk('public')->delete($oldPath);
                }

                // Store the voice file with organized path structure
                $userId = Auth::id();
                $productId = $copy->product_id;
                $timestamp = time();
                $extension = $file->getClientOriginalExtension();
                $filename = "{$timestamp}-" . uniqid() . ".{$extension}";

                $path = $file->storeAs(
                    "product-voices/{$userId}/{$productId}",
                    $filename,
                    'public'
                );

                $url = Storage::url($path);

                // Update the copy with the new voice URL
                $copy->update([
                    'voice_url_link' => $url,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Voice uploaded successfully',
                    'path' => $path,
                    'url' => $url,
                    'copy_id' => $copy->id,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No voice file provided',
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading voice file', [
                'copy_id' => $copy->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the voice file',
            ], 500);
        }
    }
}
