<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdCreative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdCreativeController extends Controller
{
    /**
     * Update the specified ad creative.
     */
    public function update(Request $request, AdCreative $adCreative)
    {
        // Admin tokens can update any ad creative, regular tokens only their own
        if (! auth()->user()->canAccessResource($adCreative)) {
            return response()->json([
                'message' => 'Forbidden. This ad creative does not belong to you.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'page_id' => ['nullable', 'string', 'max:255'],
                'instagram_user_id' => ['nullable', 'string', 'max:255'],
                'video_id' => ['nullable', 'string'],
                'original_video_url' => ['nullable', 'url'],
                'processed_video_url' => ['nullable', 'url'],
                'title' => ['nullable', 'string', 'max:255'],
                'message' => ['nullable', 'string'],
                'thumbnail_url' => ['nullable', 'url'],
                'call_to_action_type' => ['nullable', 'string', 'in:SHOP_NOW,LEARN_MORE,SIGN_UP,BUY_NOW,CONTACT_US,DOWNLOAD,BOOK_TRAVEL,APPLY_NOW,SUBSCRIBE,GET_QUOTE,WATCH_MORE,SEE_MENU,CALL_NOW,MESSAGE_PAGE,SEND_MESSAGE,WHATSAPP_MESSAGE,GET_OFFER,GET_SHOWTIMES'],
                'call_to_action_link' => ['nullable', 'url'],
                'creative_id' => ['nullable', 'string', 'max:255'],
            ]);

            $adCreative->update($validated);

            Log::info('Ad creative updated successfully', [
                'ad_creative_id' => $adCreative->id,
                'updated_by' => auth()->id(),
                'updated_fields' => array_keys($validated),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ad creative updated successfully',
                'data' => [
                    'id' => $adCreative->id,
                    'user_id' => $adCreative->user_id,
                    'product_id' => $adCreative->product_id,
                    'type' => $adCreative->type,
                    'original_video_url' => $adCreative->original_video_url,
                    'processed_video_url' => $adCreative->processed_video_url,
                    'video_id' => $adCreative->video_id,
                    'thumbnail_url' => $adCreative->thumbnail_url,
                    'creative_id' => $adCreative->creative_id,
                    'title' => $adCreative->title,
                    'message' => $adCreative->message,
                    'call_to_action_type' => $adCreative->call_to_action_type,
                    'call_to_action_link' => $adCreative->call_to_action_link,
                    'page_id' => $adCreative->page_id,
                    'instagram_user_id' => $adCreative->instagram_user_id,
                    'updated_at' => $adCreative->updated_at,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating ad creative', [
                'ad_creative_id' => $adCreative->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the ad creative',
            ], 500);
        }
    }
}
