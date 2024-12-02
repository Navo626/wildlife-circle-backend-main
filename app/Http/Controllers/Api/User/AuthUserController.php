<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

class AuthUserController extends Controller
{
    /**
     * Check if the provided token is valid.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkToken(Request $request): JsonResponse
    {
        try {
            // Get the user data and convert it to an array
            $user = $request->user();

            // Check if the user is active
            if (!$user->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active.',
                ], 403);
            }

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Token is valid',
                'data' => $user,
            ]);

        } catch (Throwable $th) {
            DB::rollBack();

            // Log the exception message and return a JSON response with an error status
            Log::error($th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Retrieve the authenticated user
     *
     * @param Request $request
     * @return mixed
     */
    public function getAuthUser(Request $request): mixed
    {
        // Retrieve the token from the Authorization header
        $token = $request->bearerToken();

        // If the token is present, try to find the associated user
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                // Return the authenticated user
                return $accessToken->tokenable;
            }
        }

        // If authentication fails or no token is provided, return a null user
        return null;
    }
}
