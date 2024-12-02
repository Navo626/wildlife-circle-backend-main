<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveProductController extends Controller
{
    // Constants for query parameters and default values
    const PAGE_QUERY_PARAM = 'page';
    const SIZE_QUERY_PARAM = 'size';
    const KEYWORD_QUERY_PARAM = 'keyword';
    const PRODUCT_ID_QUERY_PARAM = 'id';
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
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $products = Product::query()
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('highlights', 'like', '%' . $keyword . '%')
                        ->orWhere('color', 'like', '%' . $keyword . '%')
                        ->orWhere('size', 'like', '%' . $keyword . '%')
                        ->orWhere('price', 'like', '%' . $keyword . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            // Explode the image_path field into an array
            $products->getCollection()->transform(function ($product) {
                $product->image_path = explode(',', $product->image_path);
                return $product;
            });

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
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

    public function getProductsPublic(Request $request): JsonResponse
    {
        try {
            // Execute getSingleProduct if the product ID query parameter is present
            if ($request->query(self::PRODUCT_ID_QUERY_PARAM)) {
                return $this->getSingleProduct($request);
            }

            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, 8);

            $products = Product::paginate($size, ['*'], 'page', $pageNumber);

            // Explode the image_path field into an array
            $products->getCollection()->transform(function ($product) {
                $product->image_path = explode(',', $product->image_path);
                return $product;
            });

            // Apply setVisible on each product in the collection to only show certain fields
            $products->getCollection()->transform(function ($product) {
                return $product->setVisible(['id', 'title', 'image_path', 'price']);
            });

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
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

    public function getSingleProduct(Request $request): JsonResponse
    {
        try {
            $allSizes = [
                ["name" => "XXS", "inStock" => false],
                ["name" => "XS", "inStock" => false],
                ["name" => "S", "inStock" => false],
                ["name" => "M", "inStock" => false],
                ["name" => "L", "inStock" => false],
                ["name" => "XL", "inStock" => false],
                ["name" => "2XL", "inStock" => false],
                ["name" => "3XL", "inStock" => false],
            ];

            $colorClasses = [
                "White" => "white",
                "Gray" => "gray",
                "Black" => "black",
                "Red" => "red",
                "Orange" => "orange",
                "Yellow" => "yellow",
                "Green" => "green",
                "Blue" => "blue",
                "Indigo" => "indigo",
                "Purple" => "purple",
                "Pink" => "pink",
                "Brown" => "brown"
            ];

            $product = Product::find($request->query(self::PRODUCT_ID_QUERY_PARAM));

            $product->makeHidden(['created_at', 'updated_at']);

            $product->image_path = explode(',', $product->image_path);
            $productColors = explode(', ', $product->color);
            $productSizes = explode(', ', $product->size);
            $productHighlights = explode(', ', $product->highlights);

            $productColors = array_map(function ($colorName) use ($colorClasses) {
                return [
                    "name" => $colorName,
                    "value" => $colorClasses[$colorName] ?? 'white'
                ];
            }, $productColors);

            // If DB size is "-" return "-"
            if ($product->size !== "-") {
                foreach ($allSizes as &$size) {
                    if (in_array($size['name'], $productSizes)) {
                        $size['inStock'] = true;
                    }
                }
                $product->size = $allSizes;
            }

            $product->color = $productColors;
            $product->highlights = $productHighlights;

            return response()->json([
                'status' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
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
