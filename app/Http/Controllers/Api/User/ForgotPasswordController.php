<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Mail\PasswordResetEmail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class ForgotPasswordController extends Controller
{
    /**
     * Send a password reset link to the user's email.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Rate Limiting
            $key = 'reset_password_' . $request->input('email');
            $maxAttempts = 1;
            $decaySeconds = 60;

            // Check if the user has exceeded the maximum number of attempts
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'status' => false,
                    'message' => 'Too many password reset attempts. Try again in ' . $seconds . ' seconds.',
                ], 429);
            }

            // Retrieve user by email
            $user = User::where('email', $request->input('email'))->first();

            // Check if the user is active
            if (!$user->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active.',
                ], 403);
            }

            // Generate password reset token
            $reset_token = Password::createToken($user);

            // Generate reset link
            $reset_link = url("http://localhost:3000/reset-password/$reset_token?email=" . urlencode($request->input('email')));

            // Send password reset email
            Mail::to($request->input('email'))->send(new PasswordResetEmail($reset_link));

            // Increase rate limiter
            RateLimiter::hit($key, $decaySeconds);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent successfully. Please check your email.',
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
     * Reset the password of the user using the provided token.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Reset the password
            $status = Password::reset(
                [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'token' => $request->input('reset_token'),
                ],
                function (User $user, string $password) {
                    // Update the user's password
                    $user->update([
                        'password' => Hash::make($password)
                    ]);

                    // Dispatch the event
                    event(new PasswordReset($user));
                }
            );

            // Send the appropriate response
            if ($status === Password::PASSWORD_RESET) {
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Password reset successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Error resetting password',
                ], 500);
            }
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
