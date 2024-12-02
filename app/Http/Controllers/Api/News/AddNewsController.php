<?php

namespace App\Http\Controllers\Api\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\AddNewsRequest;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AddNewsController extends Controller
{
    /**
     * Add a new news
     *
     * @param AddNewsRequest $request
     * @return JsonResponse
     */
    public function addNews(AddNewsRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $images = $request->file('image');
            $imagePaths = [];

            if ($images) {
                foreach ($images as $image) {
                    $imagePath = $image->store('public/news');
                    $urlPath = Storage::url($imagePath);
                    $imagePaths[] = $urlPath;
                }
            }

            $imagePathsString = !empty($imagePaths) ? implode(',', $imagePaths) : null;

            News::create([
                'image_path' => $imagePathsString,
                'title' => $request->input('title'),
                'body' => $request->input('body'),
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'News added successfully',
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
