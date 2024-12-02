<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegisterUserController extends Controller
{
    /**
     * Create a new user and send a welcome email.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function registerUser(RegisterUserRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Create user in the database
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            DB::commit();
            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
            ], 201);
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
