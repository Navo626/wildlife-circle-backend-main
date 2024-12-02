<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AddOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AddOrderController extends Controller
{
    /**
     * Add an order
     *
     * @param AddOrderRequest $request
     * @return JsonResponse
     */
    public function addOrder(AddOrderRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Decrease the item stock
            $item = Product::find($request->input('product_id'));
            $item->stock -= $request->input('quantity');
            $item->save();

            Order::create([
                'order_id' => $request->input('order_id'),
                'product_id' => $request->input('product_id'),
                'size' => $request->input('size'),
                'color' => $request->input('color'),
                'quantity' => $request->input('quantity'),
                'amount' => $request->input('amount'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
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
