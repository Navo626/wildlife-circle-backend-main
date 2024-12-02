<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\EditProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EditProductController extends Controller
{
    /**
     * Edit a product
     *
     * @param EditProductRequest $request
     * @return JsonResponse
     */
    public function editProduct(EditProductRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Find the product by ID
            $productOld = Product::find($id);

            // Check if the product exists
            if (!$productOld) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // Prepare the data for update
            $data = $request->only([
                'title',
                'description',
                'color',
                'size',
                'price',
                'stock',
            ]);

            if ($request->hasFile('image')) {
                // Delete old images
                $oldImagePaths = explode(',', $productOld->image_path);

                foreach ($oldImagePaths as $oldImagePath) {
                    $oldImagePath = str_replace('/storage', 'public', $oldImagePath);
                    Storage::delete($oldImagePath);
                }

                // Save images to storage
                $imageFiles = $request->file('image');
                $imagePaths = [];

                foreach ($imageFiles as $image) {
                    $imagePath = $image->store('public/product');
                    $urlPath = Storage::url($imagePath);
                    $imagePaths[] = $urlPath;
                }

                $imagePathsString = implode(',', $imagePaths);

                $data['image_path'] = $imagePathsString;
            }

            // Update the product
            $productOld->update($data);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
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
