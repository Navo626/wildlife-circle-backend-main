<?php

namespace App\Http\Controllers\Api\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\EditNewsRequest;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EditNewsController extends Controller
{
    /**
     * Edit a news
     *
     * @param EditNewsRequest $request
     * @return JsonResponse
     */
    public function editNews(EditNewsRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Find the news by ID
            $newsOld = News::find($id);

            // Check if the news exists
            if (!$newsOld) {
                return response()->json([
                    'status' => false,
                    'message' => 'News not found',
                ], 404);
            }

            // Prepare the data for update
            $data = $request->only([
                'title',
                'body',
            ]);

            if ($request->hasFile('image')) {
                // Delete old images
                $oldImagePaths = explode(',', $newsOld->image_path);

                foreach ($oldImagePaths as $oldImagePath) {
                    $oldImagePath = str_replace('/storage', 'public', $oldImagePath);
                    Storage::delete($oldImagePath);
                }

                // Save images to storage
                $imageFiles = $request->file('image');
                $imagePaths = [];

                foreach ($imageFiles as $image) {
                    $imagePath = $image->store('public/news');
                    $urlPath = Storage::url($imagePath);
                    $imagePaths[] = $urlPath;
                }

                $imagePathsString = implode(',', $imagePaths);

                $data['image_path'] = $imagePathsString;
            }

            // Update the news
            $newsOld->update($data);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'News updated successfully',
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
