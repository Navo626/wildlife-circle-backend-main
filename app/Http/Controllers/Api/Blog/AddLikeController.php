<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AddLikeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleLike(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            $id = $request->query('id');

            // Check if the user has already liked the blog
            $like = Like::where('blog_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if ($like) {
                // Remove the like
                $like->delete();
            } else {
                // Add the like
                Like::create([
                    'blog_id' => $id,
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Like status updated successfully',
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
