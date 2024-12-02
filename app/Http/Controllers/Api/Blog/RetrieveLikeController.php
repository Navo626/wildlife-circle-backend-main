<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveLikeController extends Controller
{
    /**
     * Retrieve the like status of a blog
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLikeStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $id = $request->query('id');

            // Check if the user has already liked the blog
            $like = Like::where('blog_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$like) {
                return response()->json([
                    'status' => true,
                    'message' => 'The user has not liked this blog',
                    'data' => [
                        'is_liked' => false
                    ]
                ]);
            }

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'The user has liked this blog',
                'data' => [
                    'is_liked' => true
                ]
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
