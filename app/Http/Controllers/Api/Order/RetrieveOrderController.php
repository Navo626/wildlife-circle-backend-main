<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveOrderController extends Controller
{
    // Constants for query parameters and default values
    const PAGE_QUERY_PARAM = 'page';
    const SIZE_QUERY_PARAM = 'size';
    const KEYWORD_QUERY_PARAM = 'keyword';
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
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $orders = Order::query()
                ->with(['product:id,title,image_path'])
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('order_id', 'like', '%' . $keyword . '%')
                        ->orWhere('color', 'like', '%' . $keyword . '%')
                        ->orWhere('size', 'like', '%' . $keyword . '%')
                        ->orWhere('first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('last_name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%')
                        ->orWhere('phone', 'like', '%' . $keyword . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            // make hidden product_id
            $orders->makeHidden('product_id');

            return response()->json([
                'status' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $orders
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
     * Get the last order
     *
     * @return JsonResponse
     */
    public function getLastOrder(): JsonResponse
    {
        try {
            $orders = Order::query()
                ->with(['product:id,title,image_path'])
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->get();

            // Transform image_path to an array, or set it to null if it's null in the database
            $orders->transform(function ($order) {
                $order->product->image_path = $order->product->image_path ? explode(',', $order->product->image_path) : null;
                return $order;
            });

            return response()->json([
                'status' => true,
                'message' => 'Last order retrieved successfully',
                'data' => $orders
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
