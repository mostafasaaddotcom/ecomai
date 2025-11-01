<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiServiceKeyResource;
use App\Models\ApiServiceKey;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiServiceKeyController extends Controller
{
    /**
     * Get API service keys for the authenticated user or a specified user (admin only).
     */
    public function show(Request $request): ApiServiceKeyResource|JsonResponse
    {
        // Check if requesting another user's keys
        $userId = $request->query('user_id');

        if ($userId) {
            // Only admin tokens can access other users' keys
            if (!auth()->user()->hasAdminToken()) {
                return response()->json([
                    'message' => 'Forbidden. Only admin tokens can access other users\' API service keys.',
                ], 403);
            }

            // Get the specified user
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $apiServiceKeys = $user->apiServiceKeys;
        } else {
            // Get keys for authenticated user
            $apiServiceKeys = auth()->user()->apiServiceKeys;
        }

        // Return 404 if no keys found
        if (!$apiServiceKeys) {
            return response()->json([
                'message' => 'No API service keys found.',
            ], 404);
        }

        return new ApiServiceKeyResource($apiServiceKeys);
    }
}
