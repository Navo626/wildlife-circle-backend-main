<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\EditMemberRequest;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EditMemberController extends Controller
{
    /**
     * Edit a member in the database
     *
     * @param EditMemberRequest $request
     * @return JsonResponse
     */
    public function editMember(EditMemberRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Find the member by ID
            $memberOld = Member::find($id);

            // Check if the image exists
            if (!$memberOld) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member not found',
                ], 404);
            }

            // Prepare the data for update
            $data = $request->only([
                'honorary_title',
                'name',
                'position',
                'category',
                'email',
                'social_facebook',
                'social_researchgate',
                'social_scholar',
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image
                $oldImagePath = str_replace('/storage', 'public', $memberOld->image_path);
                Storage::delete($oldImagePath);

                // Save image to storage
                $imageFile = $request->file('image');
                $imagePath = $imageFile->store('public/members');

                // Convert the storage path to a URL path that can be accessed from the React server
                $urlPath = Storage::url($imagePath);

                $data['image_path'] = $urlPath;
            }

            // Update the member
            $memberOld->update($data);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Member updated successfully',
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
