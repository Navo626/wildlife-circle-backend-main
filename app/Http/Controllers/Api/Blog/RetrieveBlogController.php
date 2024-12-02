<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Api\User\AuthUserController;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveBlogController extends Controller
{
    // Constants for query parameters and default values
    const PAGE_QUERY_PARAM = 'page';
    const SIZE_QUERY_PARAM = 'size';
    const KEYWORD_QUERY_PARAM = 'keyword';
    const BLOG_ID_QUERY_PARAM = 'id';
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
    public function getBlogs(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $blogs = Blog::query()
                ->whereHas('user', function ($query) {
                    $query->where('is_active', '<>', 0);
                })
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('body', 'like', '%' . $keyword . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            return response()->json([
                'status' => true,
                'message' => 'Blogs retrieved successfully',
                'data' => $blogs
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
     * Get all public blogs or a single blog item with the given ID
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function getBlogsPublic(Request $request): JsonResponse
    {
        try {
            // Execute getSingleProduct if the product ID query parameter is present
            if ($request->query(self::BLOG_ID_QUERY_PARAM)) {
                return $this->getSingleBlog($request);
            }

            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, 6);

            $blog = Blog::query()
                ->whereHas('user', function ($query) {
                    $query->where('is_active', '<>', 0);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            // Transform the collection to add the author name to each blog item
            $blog->getCollection()->transform(function ($blogItem) {
                // Get the author name from the user table
                $user = User::find($blogItem->user_id);
                $blogItem->author = $user->first_name . ' ' . $user->last_name;

                return $blogItem;
            });

            // Apply setVisible on each blog item in the collection to only show certain fields
            $blog->getCollection()->transform(function ($blogItem) {
                return $blogItem->setVisible(['id', 'title', 'body', 'image_path', 'author', 'created_at', 'updated_at']);
            });

            return response()->json([
                'status' => true,
                'message' => 'Blogs retrieved successfully',
                'data' => $blog
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
     * Get a single blog item with the given ID
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Throwable If there is any error during the update process
     */
    public function getSingleBlog(Request $request): JsonResponse
    {
        try {
            // Get the blog ID from the query parameter
            $blogId = $request->query(self::BLOG_ID_QUERY_PARAM);

            // Find the blog with the given ID
            $blog = Blog::whereHas('user', function ($query) {
                $query->where('is_active', '<>', 0);
            })->find($blogId);

            if (!$blog) {
                return response()->json([
                    'status' => false,
                    'message' => 'Blog not found or the author is not active',
                ], 404);
            }

            // Get the author name from the user table
            $user = User::find($blog->user_id);

            // Get the number of articles written by the author
            $user->setAttribute('articles_written', Blog::where('user_id', $user->id)->count());

            // Get the total number of likes on the blog
            $likes = Like::where('blog_id', $blog->id)->get();
            $blog->setAttribute('likes', $likes->count());

            // Get the total number of comments on the blog
            $comments = Comment::where('blog_id', $blog->id)->get();
            $blog->setAttribute('comments', $comments->count());

            // Get the authenticated user
            $authObj = new AuthUserController();
            $authUser = $authObj->getAuthUser($request);

            $is_mine = false;

            if ($authUser) {
                $is_mine = $authUser->id === $blog->user_id;
            }

            // Apply setVisible on the blog item to only show certain fields
            $blog->setVisible(['author', 'title', 'body', 'image_path', 'likes', 'comments', 'created_at']);

            // Return a JSON response with the blog data and the author name
            return response()->json([
                'status' => true,
                'message' => 'Blog retrieved successfully',
                'data' => [
                    'blog' => $blog,
                    'user' => [
                        'is_mine' => $is_mine,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'image_path' => $user->image_path ?? null,
                        'articles_written' => $user->articles_written,
                        'created_at' => $user->created_at,
                    ]
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
