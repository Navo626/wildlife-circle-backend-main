<?php

namespace App\Http\Controllers\Api\Session;

use App\Http\Controllers\Controller;
use App\Http\Requests\Session\EditSessionRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EditSessionController extends Controller
{
    /**
     * Edit a project
     *
     * @param EditSessionRequest $request
     * @return JsonResponse
     */
    public function editSession(EditSessionRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $id = $request->query('id');

            // Find the session by ID
            $sessionOld = Event::find($id);

            // Check if the session exists
            if (!$sessionOld) {
                return response()->json([
                    'status' => false,
                    'message' => 'Session not found',
                ], 404);
            }

            // Prepare the data for update
            $data = $request->only([
                'title',
                'description',
                'link',
                'host',
                'start_date',
                'end_date',
            ]);

            // Update the session
            $sessionOld->update($data);

            DB::commit();

            // Send the appropriate response
            return response()->json([
                'status' => true,
                'message' => 'Session updated successfully',
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
