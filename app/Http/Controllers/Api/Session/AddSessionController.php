<?php

namespace App\Http\Controllers\Api\Session;

use App\Http\Controllers\Controller;
use App\Http\Requests\Session\AddSessionRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AddSessionController extends Controller
{
    /**
     * Add a new session
     *
     * @param AddSessionRequest $request
     * @return JsonResponse
     */
    public function addSession(AddSessionRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            Event::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'link' => $request->input('link'),
                'host' => $request->input('host'),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Session added successfully',
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
