<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RetrieveProjectController extends Controller
{
    // Constants for query parameters and default values
    const PAGE_QUERY_PARAM = 'page';
    const SIZE_QUERY_PARAM = 'size';
    const KEYWORD_QUERY_PARAM = 'keyword';
    const PROJECT_ID_QUERY_PARAM = 'id';
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
    public function getProjects(Request $request): JsonResponse
    {
        try {
            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, self::DEFAULT_SIZE);
            $keyword = $request->query(self::KEYWORD_QUERY_PARAM);

            $products = Project::query()
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('body', 'like', '%' . $keyword . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $pageNumber);

            // Transform image_path to an array, or set it to null if it's null in the database
            $products->getCollection()->transform(function ($product) {
                $product->image_path = $product->image_path ? explode(',', $product->image_path) : null;
                return $product;
            });

            return response()->json([
                'status' => true,
                'message' => 'Projects retrieved successfully',
                'data' => $products
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
     * Get all projects
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProjectsPublic(Request $request): JsonResponse
    {
        try {
            // Execute getSingleProject if the project ID query parameter is present
            if ($request->query(self::PROJECT_ID_QUERY_PARAM)) {
                return $this->getSingleProject($request);
            }

            $pageNumber = $request->query(self::PAGE_QUERY_PARAM, self::DEFAULT_PAGE_NUMBER);
            $size = $request->query(self::SIZE_QUERY_PARAM, 6);

            $projects = Project::orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $pageNumber);

            // Transform image_path to an array, or set it to null if it's null in the database
            $projects->getCollection()->transform(function ($project) {
                $project->image_path = $project->image_path ? explode(',', $project->image_path) : null;
                return $project;
            });

            // Apply setVisible on each project in the collection to only show certain fields
            $projects->getCollection()->transform(function ($project) {
                return $project->setVisible(['id', 'title', 'body', 'created_at', 'updated_at', 'image_path']);
            });

            return response()->json([
                'status' => true,
                'message' => 'Projects retrieved successfully',
                'data' => $projects
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
     * Get a single project
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getSingleProject(Request $request): JsonResponse
    {
        try {
            $projectId = $request->query(self::PROJECT_ID_QUERY_PARAM);

            $project = Project::find($projectId);

            // Transform image_path to an array, or set it to null if it's null in the database
            $project->image_path = $project->image_path ? explode(',', $project->image_path) : null;

            return response()->json([
                'status' => true,
                'message' => 'Project retrieved successfully',
                'data' => $project
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
