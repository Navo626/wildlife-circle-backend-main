<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AddProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AddProductController extends Controller
{
    /**
     * Add a new product
     *
     * @param AddProductRequest $request
     * @return JsonResponse
     */
    public function addProduct(AddProductRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Save image to storage
            $images = $request->file('image');
            $imagePaths = [];

            if ($images) {
                foreach ($images as $image) {
                    $imagePath = $image->store('public/product');
                    $urlPath = Storage::url($imagePath);
                    $imagePaths[] = $urlPath;
                }
            }

            $imagePathsString = implode(',', $imagePaths);

            Product::create([
                'image_path' => $imagePathsString,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'color' => $request->input('color'),
                'size' => $request->input('size') ? $request->input('size') : "-",
                'price' => $request->input('price'),
                'stock' => $request->input('stock'),
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Product added successfully',
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
