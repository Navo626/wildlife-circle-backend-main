<?php

namespace App\Http\Controllers\Api\Gallery;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveGalleryController extends Controller
{
    // Constants for query parameters and default values
    const PAGE_QUERY_PARAM = 'page';
    const SIZE_QUERY_PARAM = 'size';
    const KEYWORD_QUERY_PARAM = 'keyword';
    const DEFAULT_PAGE_NUMBER = 1;
    const DEFAULT_SIZE = 10;

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function getGallery(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $gallery = Gallery::query()
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('captured_by', 'like', '%' . $keyword . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            return response()->json([
                'status' => true,
                'message' => 'Gallery retrieved successfully',
                'data' => $gallery
            ]);
        } catch (Throwable $th) {
            // Log the exception message and return a JSON response with an error status
            Log::error($th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function getGalleryPublic(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);

            $images = Gallery::orderBy('created_at', 'desc')->paginate(5, ['*'], 'page', $pageNumber);

            // Remove id, created_at and updated_at fields from the response
            $images->makeHidden(['id', 'created_at', 'updated_at']);

            return response()->json([
                'status' => true,
                'message' => 'Gallery retrieved successfully',
                'data' => $images
            ]);
        } catch (Throwable $th) {
            // Log the exception message and return a JSON response with an error status
            Log::error($th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
