<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeleteMemberController extends Controller
{
    /**
     * Delete a member from the database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMember(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Delete image from storage
            $image = Member::find($id);
            $imagePath = str_replace('/storage', 'public', $image->image_path);
            Storage::delete($imagePath);

            // Delete member details from the database
            Member::where('id', $id)->delete();

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Image deleted successfully',
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
