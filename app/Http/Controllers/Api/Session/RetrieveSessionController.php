<?php

namespace App\Http\Controllers\Api\Session;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveSessionController extends Controller
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
    public function getSessions(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            // Retrieve sessions based on the keyword, order by end_date in descending order, and paginate the results
            $sessions = Event::query()
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('host', 'like', '%' . $keyword . '%');
                })
                ->orderBy('end_date', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            // Add 'status' to each session
            $sessions->getCollection()->transform(function ($session) {
                $session->status = Carbon::parse($session->end_date)->isPast() ? 'Ended' : 'Ongoing';
                return $session;
            });

            // Return a JSON response with a success status, message, and the paginated sessions
            return response()->json([
                'status' => true,
                'message' => 'Sessions retrieved successfully',
                'data' => $sessions
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

    public function getSessionsPublic(): JsonResponse
    {
        try {
            // Retrieve all sessions
            $sessions = Event::all();

            // Map the sessions to a new array with the required keys
            $data = $sessions->map(function ($session) {
                return [
                    'id' => (string)$session->id,
                    'resourceId' => (string)$session->id,
                    'start' => Carbon::parse($session->start_date)->format('Y-m-d\TH:i:s'),
                    'end' => Carbon::parse($session->end_date)->format('Y-m-d\TH:i:s'),
                    'title' => $session->title,
                    'description' => $session->description,
                    'link' => $session->link,
                    'host' => $session->host,
                ];
            });

            // Return a JSON response with the data
            return response()->json([
                'status' => true,
                'message' => 'Session retrieved successfully',
                'data' => $data
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
