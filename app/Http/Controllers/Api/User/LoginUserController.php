<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginUserRequest;
use App\Services\CookieHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoginUserController extends Controller
{
    // Inject the CookieHandler service
    protected CookieHandler $cookieHandler;

    // Constructor
    public function __construct(CookieHandler $cookieHandler)
    {
        $this->cookieHandler = $cookieHandler;
    }

    /**
     * Handle user login.
     *
     * @param LoginUserRequest $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function loginUser(LoginUserRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Attempt user authentication
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or Password is incorrect.',
                ], 401);
            }

            // Retrieve authenticated user
            $user = $request->user();

            // Check if the user is active
            if (!$user->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active.',
                ], 403);
            }

            // Set token expiration based on 'remember me' option
            $expiration = $request->input('remember') === true ? now()->addDays(30) : now()->addHours(3);

            // Create a token
            $token = $user->createToken("API TOKEN", ['*'], $expiration)->plainTextToken;

            // Encrypt the token
            $encryptedToken = Crypt::encryptString($token);

            // Set cookie expiration based on 'remember me' option
            $expirationCookie = $request->input('remember') === true ? time() + 30 * 24 * 3600 : time() + 3 * 3600;

            // Set the token as a cookie
            $this->cookieHandler->setCookie(
                'token',
                $encryptedToken,
                [
                    'expires' => $expirationCookie,
                    'path' => env('COOKIE_PATH', '/'),
                    'domain' => env('COOKIE_DOMAIN', 'localhost'),
                    'samesite' => env('COOKIE_SAMESITE', 'Strict'),
                    'secure' => env('COOKIE_SECURE', false),
                    'httponly' => env('COOKIE_HTTP_ONLY', false),
                ]
            );

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $encryptedToken,
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
}
