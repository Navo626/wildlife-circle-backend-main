<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Api\User\AuthUserController;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveCommentController extends Controller
{
    /**
     * Retrieve comments for a blog post
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function getCommentsPublic(Request $request): JsonResponse
    {
        try {
            // Retrieve the blog ID from the query parameters
            $blogId = $request->query('id');

            // Get the authenticated user
            $authObj = new AuthUserController();
            $user = $authObj->getAuthUser($request);

            // Retrieve all the comments for the blog post
            $comments = Comment::orderBy('created_at', 'desc')
                ->where('blog_id', $blogId)
                ->whereHas('user', function ($query) {
                    $query->where('is_active', '<>', 0);
                })
                ->get();

            // Add the name of the user who made the comment
            $comments->transform(function ($commentItem) {
                $user = User::find($commentItem->user_id);
                $commentItem->setAttribute('first_name', $user->first_name);
                $commentItem->setAttribute('last_name', $user->last_name);
                $commentItem->setAttribute('image_path', $user->image_path);
                return $commentItem;
            });

            if ($user) {
                // Add a field to each comment to indicate if the authenticated user made the comment
                $comments->transform(function ($commentItem) use ($user) {
                    if ($commentItem->user_id === $user->id) {
                        $commentItem->setAttribute('is_mine', true);
                    } else {
                        $commentItem->setAttribute('is_mine', false);
                    }
                    return $commentItem;
                });
            } else {
                // If the user is not authenticated, set the is_mine field to false
                $comments->transform(function ($commentItem) {
                    $commentItem->setAttribute('is_mine', false);
                    return $commentItem;
                });
            }

            // Apply setVisible on each blog item in the collection to only show certain fields
            $comments->transform(function ($commentItem) {
                return $commentItem->setVisible(['id', 'is_mine', 'first_name', 'last_name', 'image_path', 'body', 'created_at']);
            });

            // Return a JSON response with a success status
            return response()->json([
                'status' => true,
                'message' => 'Comments retrieved successfully',
                'data' => $comments
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
