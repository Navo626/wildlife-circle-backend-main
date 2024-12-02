<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveMemberController extends Controller
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
    public function getMembers(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $users = Member::query()
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('position', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                })
                ->paginate($size, ['*'], 'page', $pageNumber);

            return response()->json([
                'status' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
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
     * Get all members that are executive team, advisors and past presidents
     *
     * @return JsonResponse
     */
    public function getMembersPublic(): JsonResponse
    {
        try {
            // Get all members that are executive team
            $team = Member::where('category', '=', 'Executive Team')
                ->get();

            // Get all members that are advisor
            $advisors = Member::where('category', '=', 'Advisor')
                ->get();

            // Get all members that are past presidents
            $presidents = Member::where('position', '=', 'Past President')
                ->get();

            // Remove id, is_active, created_at and updated_at fields from the response
            $team->makeHidden(['id', 'created_at', 'updated_at']);
            $advisors->makeHidden(['id', 'created_at', 'updated_at']);
            $presidents->makeHidden(['id', 'created_at', 'updated_at']);

            return response()->json([
                'status' => true,
                'message' => 'Members retrieved successfully',
                'data' => [
                    'team' => $team,
                    'advisors' => $advisors,
                    'presidents' => $presidents,
                ]
            ]);
        } catch (Throwable $th) {
            Log::error($th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
