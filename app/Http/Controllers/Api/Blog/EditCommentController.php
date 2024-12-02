<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\EditCommentRequest;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EditCommentController extends Controller
{
    /**
     * Add a comment to a blog post.
     *
     * @param EditCommentRequest $request
     * @return JsonResponse
     */
    public function editComment(EditCommentRequest $request): JsonResponse
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the authenticated user
            $user = $request->user();

            // Get the comment ID from the request query parameters
            $id = $request->query('id');

            // Find the comment with the ID
            $comment = Comment::find($id);

            // Check if the comment exists
            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found',
                ], 404);
            }

            // Check if the authenticated user is the owner of the comment
            if ($comment->user_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to edit this comment',
                ], 403);
            }

            // Update the comment content
            $comment->body = $request->input('body');

            // Save the comment
            $comment->save();

            // Commit the transaction
            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Comment updated successfully',
            ], 201);
        } catch (Throwable $th) {
            // Rollback the transaction
            DB::rollBack();

            // Log the exception message
            Log::error($th->getMessage());

            // Send the appropriate response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
