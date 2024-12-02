<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\EditProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EditProjectController extends Controller
{
    /**
     * Edit a project
     *
     * @param EditProjectRequest $request
     * @return JsonResponse
     */
    public function editProject(EditProjectRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Find the project by ID
            $projectOld = Project::find($id);

            // Check if the project exists
            if (!$projectOld) {
                return response()->json([
                    'status' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            // Prepare the data for update
            $data = $request->only([
                'title',
                'body',
                'date',
            ]);

            if ($request->hasFile('image')) {
                // Delete old images
                $oldImagePaths = explode(',', $projectOld->image_path);

                foreach ($oldImagePaths as $oldImagePath) {
                    $oldImagePath = str_replace('/storage', 'public', $oldImagePath);
                    Storage::delete($oldImagePath);
                }

                // Save images to storage
                $imageFiles = $request->file('image');
                $imagePaths = [];

                foreach ($imageFiles as $image) {
                    $imagePath = $image->store('public/project');
                    $urlPath = Storage::url($imagePath);
                    $imagePaths[] = $urlPath;
                }

                $imagePathsString = implode(',', $imagePaths);

                $data['image_path'] = $imagePathsString;
            }

            // Update the project
            $projectOld->update($data);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Project updated successfully',
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
