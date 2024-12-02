<?php

namespace App\Http\Controllers\Api\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gallery\AddImageRequest;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AddGalleryController extends Controller
{
    /**
     * Add a new image to the gallery
     *
     * @param AddImageRequest $request
     * @return JsonResponse
     */
    public function addImage(AddImageRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Save image to storage
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('public/gallery');

            // Convert the storage path to a URL path that can be accessed from the React server
            $urlPath = Storage::url($imagePath);

            Gallery::create([
                'image_path' => $urlPath,
                'title' => $request->input('title'),
                'captured_by' => $request->input('captured_by'),
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Image added successfully',
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
