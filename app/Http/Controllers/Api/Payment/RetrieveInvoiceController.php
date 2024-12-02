<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\RetrieveInvoiceRequest;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveInvoiceController extends Controller
{
    /**
     * Generate an invoice
     *
     * @param RetrieveInvoiceRequest $request
     * @return Application|Response|JsonResponse|ApplicationContract|ResponseFactory
     */
    public function generateInvoice(RetrieveInvoiceRequest $request): Application|Response|JsonResponse|ApplicationContract|ResponseFactory
    {
        DB::beginTransaction();

        try {
            $orderId = $request->query("order_id");

            // Get the order details from the database
            $order = DB::table('orders')
                ->where('order_id', $orderId)
                ->first();

            // Append the product title to the order
            $order->title = DB::table('products')
                ->where('id', $order->product_id)
                ->value('title');

            // Convert the order date to a human-readable format
            $order->date = date('d F Y H:i:s', strtotime($order->created_at));

            // Hide unnecessary fields
            unset($order->id);
            unset($order->product_id);
            unset($order->first_name);
            unset($order->last_name);
            unset($order->email);
            unset($order->phone);
            unset($order->address);
            unset($order->updated_at);

            // Render resume view
            $html = view('invoice', ['order' => $order])->render();

            $response = Http::post(env("PDF_GENERATOR_ENDPOINT"), [
                'html' => $html,
            ]);

            // If the request is successful, commit the transaction and return the PDF
            if ($response->successful()) {
                DB::commit();

                return response($response->body(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="Invoice - ' . $request->input('order_id') . '.pdf"',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate invoice',
                ], 500);
            }
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
