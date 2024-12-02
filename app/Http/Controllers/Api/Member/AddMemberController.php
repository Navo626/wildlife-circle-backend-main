<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\AddMemberRequest;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AddMemberController extends Controller
{
    /**
     * Add a new member to the database
     *
     * @param AddMemberRequest $request
     * @return JsonResponse
     */
    public function addMember(AddMemberRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Save image to storage
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('public/members');

            // Convert the storage path to a URL path that can be accessed from the React server
            $urlPath = Storage::url($imagePath);

            // Create user in the database
            Member::create([
                'honorary_title' => $request->input('honorary_title'),
                'name' => $request->input('name'),
                'position' => $request->input('position'),
                'category' => $request->input('category'),
                'image_path' => $urlPath,
                'email' => $request->input('email'),
                'social_facebook' => $request->input('social_facebook'),
                'social_researchgate' => $request->input('social_researchgate'),
                'social_scholar' => $request->input('social_scholar'),
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Member added successfully',
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
