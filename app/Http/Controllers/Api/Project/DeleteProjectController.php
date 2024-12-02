<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeleteProjectController extends Controller
{
    /**
     * Delete a project
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteProject(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Delete image from storage
            $image = Project::find($id);
            $imagePath = str_replace('/storage', 'public', $image->image_path);
            Storage::delete($imagePath);

            // Delete project details from the database
            Project::where('id', $id)->delete();

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Project deleted successfully',
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
