<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\EditBlogRequest;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EditBlogController extends Controller
{
    /**
     * Edit a blog post
     *
     * @param EditBlogRequest $request
     * @return JsonResponse
     */
    public function editBlog(EditBlogRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Find the blog post by ID
            $blogOld = Blog::find($id);

            // Check if the blog post exists
            if (!$blogOld) {
                return response()->json([
                    'status' => false,
                    'message' => 'Blog not found',
                ], 404);
            }

            // Prepare the data for update
            $data = $request->only([
                'title',
                'body',
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image
                $oldImagePath = str_replace('/storage', 'public', $blogOld->image_path);
                Storage::delete($oldImagePath);

                // Save image to storage
                $imageFile = $request->file('image');
                $imagePath = $imageFile->store('public/blog');

                // Convert the storage path to a URL path that can be accessed from the React server
                $urlPath = Storage::url($imagePath);

                $data['image_path'] = $urlPath;
            }

            // Update the blog post
            $blogOld->update($data);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Blog updated successfully',
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
