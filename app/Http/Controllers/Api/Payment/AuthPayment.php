<?php

namespace App\Http\Controllers\Api\Payment;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthPayment extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $merchant_id = env('PAYHERE_MERCHANT_ID');
        $merchant_secret = env('PAYHERE_MERCHANT_SECRET');
        $order_id = uniqid();
        $currency = env('PAYHERE_CURRENCY');
        $amount = $request->input('amount');

        $hash = strtoupper(
            md5(
                $merchant_id .
                $order_id .
                number_format($amount, 2, '.', '') .
                $currency .
                strtoupper(md5($merchant_secret))
            )
        );

        return response()->json([
            'status' => true,
            'message' => 'Payment initiated successfully',
            'data' => [
                'merchant_id' => $merchant_id,
                'order_id' => $order_id,
                'currency' => $currency,
                'hash' => $hash,
                'amount' => $amount,
            ]
        ]);
    }
}
