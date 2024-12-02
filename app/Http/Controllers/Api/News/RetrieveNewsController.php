<?php

namespace App\Http\Controllers\Api\News;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveNewsController extends Controller
{
    // Constants for query parameters and default values
    const PAGE_QUERY_PARAM = 'page';
    const SIZE_QUERY_PARAM = 'size';
    const KEYWORD_QUERY_PARAM = 'keyword';
    const NEWS_ID_QUERY_PARAM = 'id';
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
    public function getNews(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $news = News::query()
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('body', 'like', '%' . $keyword . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            // Transform image_path to an array, or set it to null if it's null in the database
            $news->getCollection()->transform(function ($newsItem) {
                $newsItem->image_path = $newsItem->image_path ? explode(',', $newsItem->image_path) : null;
                return $newsItem;
            });

            return response()->json([
                'status' => true,
                'message' => 'News retrieved successfully',
                'data' => $news
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
    public function getNewsPublic(Request $request): JsonResponse
    {
        try {
            // Execute getSingleProduct if the product ID query parameter is present
            if ($request->query(self::NEWS_ID_QUERY_PARAM)) {
                return $this->getSingleNews($request);
            }

            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);

            $news = News::orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $pageNumber);

            // Transform image_path to an array, or set it to null if it's null in the database
            $news->getCollection()->transform(function ($newsItem) {
                $newsItem->image_path = $newsItem->image_path ? explode(',', $newsItem->image_path) : null;
                return $newsItem;
            });

            // Apply setVisible on each news item in the collection to only show certain fields
            $news->getCollection()->transform(function ($newsItem) {
                return $newsItem->setVisible(['id', 'title', 'body', 'image_path', 'created_at', 'updated_at']);
            });

            return response()->json([
                'status' => true,
                'message' => 'News retrieved successfully',
                'data' => $news
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
     * Get a single news from the database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSingleNews(Request $request): JsonResponse
    {
        try {
            $newsId = $request->query(self::NEWS_ID_QUERY_PARAM);

            $news = News::find($newsId);

            // Transform image_path to an array, or set it to null if it's null in the database
            $news->image_path = $news->image_path ? explode(',', $news->image_path) : null;

            return response()->json([
                'status' => true,
                'message' => 'News retrieved successfully',
                'data' => $news
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
