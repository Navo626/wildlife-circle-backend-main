<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateProfileController extends Controller
{
    /**
     * Update the user's profile.
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            // Check if the user is active
            if (!$user->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active.',
                ], 403);
            }

            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->phone = $request->input('phone');

            if ($request->hasFile('image')) {
                // Delete the existing image if it exists
                if ($user->image_path) {
                    Storage::delete($user->image_path);
                }

                $imageFile = $request->file('image');
                $imagePath = $imageFile->store('public/users');

                // Convert the storage path to a URL path that can be accessed from the React server
                $urlPath = Storage::url($imagePath);
                $user->image_path = $urlPath;
            }

            $user->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
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
