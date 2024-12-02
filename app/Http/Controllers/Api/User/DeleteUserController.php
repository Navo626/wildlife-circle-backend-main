<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteUserController extends Controller
{
    /**
     * Delete a user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteUser(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Prevent deactivation of the currently authenticated user
            if ($id == $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot delete yourself',
                ], 400);
            }

            // Retrieve the user
            $user = User::find($id);

            // Deactivate user and invalidate all tokens
            $user->update(['is_active' => 0]);
            $user->tokens()->delete();

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully',
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
