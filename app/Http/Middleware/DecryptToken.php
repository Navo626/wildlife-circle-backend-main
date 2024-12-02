<?php

namespace App\Http\Middleware;

use App\Services\CookieHandler;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DecryptToken
{
    // Inject the CookieHandler service
    protected CookieHandler $cookieHandler;

    // Constructor
    public function __construct(CookieHandler $cookieHandler)
    {
        $this->cookieHandler = $cookieHandler;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request has an 'Authorization' header
        if ($request->hasHeader('Authorization')) {
            // Extract the token from the 'Authorization' header
            $encryptedToken = str_replace('Bearer ', '', $request->header('Authorization'));

            try {
                // Try to decrypt the token
                $decryptedToken = Crypt::decryptString($encryptedToken);

                // Retrieve the token from the database
                $token = PersonalAccessToken::findToken($decryptedToken);

                // Check if the token exists
                if ($token) {
                    // If the token's expiration is before now, return 'unauthenticated'
                    if ($token->expires_at->lt(now())) {
                        return response()->json([
                            'message' => 'Unauthenticated.',
                        ], 401);
                    }

                    // If the token's expiration is less than now + 3 hours, update its expiration
                    if ($token->expires_at->lt(now()->addHours(3))) {
                        $token->forceFill([
                            'expires_at' => now()->addHours(3),
                        ])->save();

                        $expirationCookie = time() + 3 * 3600;

                        // Increase the 'token' cookie expiration time to 3 hours
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
                    }

                    // Replace the 'Authorization' header with the decrypted token
                    $request->headers->set('Authorization', 'Bearer ' . $decryptedToken);
                }
            } catch (Throwable $th) {
                // Log the exception message and return a JSON response with an error status
                Log::error($th->getMessage());

                // If decryption fails, return a JSON response with a status of 'unauthenticated'
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        }

        // Pass the request to the next middleware
        return $next($request);
    }
}
