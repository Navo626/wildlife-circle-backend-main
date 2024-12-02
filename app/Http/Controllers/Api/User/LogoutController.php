<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LogoutRequest;
use App\Services\CookieHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogoutController extends Controller
{
    // Inject the CookieHandler service
    protected CookieHandler $cookieHandler;

    // Constructor
    public function __construct(CookieHandler $cookieHandler)
    {
        $this->cookieHandler = $cookieHandler;
    }

    /**
     * Handle logout and logoutAll methods
     *
     * @param LogoutRequest $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function handle(LogoutRequest $request): JsonResponse
    {
        return $request->input('logout_all') === true ? $this->logoutAll($request) : $this->logout($request);
    }

    /**
     * Log out the current user and revoke their access token.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function logout(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Revoke the current user's access token
            $request->user()->currentAccessToken()->delete();

            // Invalidate the token cookie
            $this->deleteCookie($request);

            DB::commit();
            // Return a success response
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
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
     * Log out the current user from all devices and revoke all their tokens.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function logoutAll(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Revoke all the current user's tokens
            $request->user()->tokens()->delete();

            // Invalidate the token cookie
            $this->deleteCookie($request);

            DB::commit();
            // Return a success response
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out from all devices',
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
     * Invalidate token cookie.
     *
     * @param Request $request
     * @return void
     */
    private function deleteCookie(Request $request): void
    {
        // Expire 'token' cookie
        $this->cookieHandler->setCookie('token', '', ['expires' => time() - 3600, 'path' => env('COOKIE_PATH', '/'),]);
    }
}
