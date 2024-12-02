<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactUsRequest;
use App\Mail\ContactUsEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactUsController extends Controller
{
    /**
     * Send contact us email.
     *
     * @param ContactUsRequest $request
     * @return JsonResponse
     */
    public function contactUs(ContactUsRequest $request): JsonResponse
    {
        try {
            // Send contact us email
            Mail::to($request->input('email'))
                ->send(new ContactUsEmail(
                    $request->input('firstname'),
                    $request->input('lastname'),
                    $request->input('phone'),
                    $request->input('email'),
                    $request->input('details')
                ));

            // Return a JSON response with a success status
            return response()->json([
                'status' => true,
                'message' => 'Feedback sent successfully.',
            ]);
        } catch (Throwable $th) {
            // Log the exception message and return a JSON response with an error status
            Log::error($th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred.',
            ], 500);
        }
    }
}
