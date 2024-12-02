<?php

namespace App\Http\Controllers\Api\Gallery;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeleteGalleryController extends Controller
{
    /**
     * Delete an image from the gallery
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteImage(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Delete image from storage
            $image = Gallery::find($id);
            $imagePath = str_replace('/storage', 'public', $image->image_path);
            Storage::delete($imagePath);

            // Delete image from the database
            Gallery::where('id', $id)->delete();

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
