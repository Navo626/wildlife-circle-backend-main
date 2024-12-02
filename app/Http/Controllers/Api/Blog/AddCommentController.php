<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\AddCommentRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AddCommentController extends Controller
{
    /**
     * Add a comment to a blog post.
     *
     * @param AddCommentRequest $request
     * @return JsonResponse
     */
    public function addComment(AddCommentRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            $id = $request->query('id');

            Comment::create([
                'blog_id' => $id,
                'user_id' => $user->id,
                'body' => $request->input('body'),
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Comment added successfully',
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
