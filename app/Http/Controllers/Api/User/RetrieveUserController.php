<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveUserController extends Controller
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
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $users = User::query()
            ->where('is_active', '<>', 0)
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('last_name', 'like', '%' . $keyword . '%')
                        ->orWhere('phone', 'like', '%' . $keyword . '%')
                        ->orWhere('role', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->orderBy('role')
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
}
