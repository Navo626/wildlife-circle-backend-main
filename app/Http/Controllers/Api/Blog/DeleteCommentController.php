<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteCommentController extends Controller
{
    /**
     * Delete a blog post
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteComment(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Delete comment from the database
            Comment::where('id', $id)->delete();

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Comment deleted successfully',
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
