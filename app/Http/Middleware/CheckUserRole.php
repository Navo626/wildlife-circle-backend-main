<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'error' => 'Unauthenticated.'
                ], 401);
            }

            // Check if the user has the specified role
            $db_role = User::where('id', $user->id)->first()->role;

            if ($role !== $db_role) {
                return response()->json([
                    'error' => 'Forbidden.'
                ], 403);
            }
        } catch (Throwable $th) {
            // Log the exception message and return a JSON response with an error status
            Log::error($th->getMessage());

            // If decryption fails, return a JSON response with a status of 'unauthenticated'
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return $next($request);
    }
}
