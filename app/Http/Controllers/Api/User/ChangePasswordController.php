<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Services\CookieHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChangePasswordController extends Controller
{
    // Inject the CookieHandler service
    protected CookieHandler $cookieHandler;

    // Constructor
    public function __construct(CookieHandler $cookieHandler)
    {
        $this->cookieHandler = $cookieHandler;
    }

    /**
     * Change the password of the authenticated user.
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Retrieve authenticated user
            $user = $request->user();

            // Check if the user is active
            if (!$user->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active.',
                ], 403);
            }

            // Check if the current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Current password is incorrect',
                    'errors' => ([
                        'current_password' => [
                            'Current password is incorrect'
                        ]
                    ])
                ], 400);
            }

            // Update the password
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            // Invalidate session credentials
            $this->invalidateCredentials($request);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully. Please log in again.',
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
     * Invalidate session credentials.
     *
     * @param Request $request
     * @return void
     */
    private function invalidateCredentials(Request $request): void
    {
        // Revoke all tokens associated with the authenticated user
        $request->user()->tokens()->delete();

        // Expire 'token' cookie
        $this->cookieHandler->setCookie('token', '', ['expires' => time() - 3600, 'path' => env('COOKIE_PATH', '/'),]);
    }
}
