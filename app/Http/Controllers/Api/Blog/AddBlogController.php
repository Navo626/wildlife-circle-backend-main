<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\AddBlogRequest;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AddBlogController extends Controller
{
    /**
     * Add a new blog post
     *
     * @param AddBlogRequest $request
     * @return JsonResponse
     */
    public function addBlog(AddBlogRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $urlPath = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('public/blog');
                $urlPath = Storage::url($imagePath);
            }

            Blog::create([
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'body' => $request->input('body'),
                'image_path' => $urlPath,
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Blog added successfully',
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
