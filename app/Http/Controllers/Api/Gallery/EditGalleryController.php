<?php

namespace App\Http\Controllers\Api\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gallery\EditImageRequest;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EditGalleryController extends Controller
{
    /**
     * Edit an image in the gallery
     *
     * @param EditImageRequest $request
     * @return JsonResponse
     */
    public function editImage(EditImageRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Find the image by ID
            $imageOld = Gallery::find($id);

            // Check if the image exists
            if (!$imageOld) {
                return response()->json([
                    'status' => false,
                    'message' => 'Image not found',
                ], 404);
            }

            // Prepare the data for update
            $data = $request->only([
                'title',
                'captured_by'
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image
                $oldImagePath = str_replace('/storage', 'public', $imageOld->image_path);
                Storage::delete($oldImagePath);

                // Save image to storage
                $imageFile = $request->file('image');
                $imagePath = $imageFile->store('public/gallery');

                // Convert the storage path to a URL path that can be accessed from the React server
                $urlPath = Storage::url($imagePath);

                $data['image_path'] = $urlPath;
            }

            // Update the image
            $imageOld->update($data);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Image updated successfully',
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
