<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Order;
use App\Models\Product;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Retrieve the counts of all resources
     *
     * @return JsonResponse
     */
    public function getCounts(): JsonResponse
    {
        try {
            // Retrieve all users count
            $userCount = User::count();

            // Retrieve all news count
            $newsCount = News::count();

            // Retrieve all projects count
            $projectCount = Project::count();

            // Retrieve all gallery count
            $galleryCount = Gallery::count();

            // Retrieve all products count
            $productCount = Product::count();

            // Retrieve all orders count
            $orderCount = Order::count();

            // Return JSON response with retrieved permissions
            return response()->json([
                'status' => true,
                'message' => 'Counts retrieved successfully',
                'data' => [
                    'users' => $userCount,
                    'news' => $newsCount,
                    'projects' => $projectCount,
                    'gallery' => $galleryCount,
                    'products' => $productCount,
                    'orders' => $orderCount,
                ]
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
     * Retrieve the monthly income
     *
     * @return JsonResponse
     */
    public function getMonthlyIncome(): JsonResponse
    {
        try {
            // Initialize an array to hold the income for each month
            $monthlyIncome = [];

            // Loop through each month
            for ($month = 1; $month <= 12; $month++) {
                // Retrieve the total income for the current month
                $income = Order::whereMonth('created_at', $month)->sum('amount');

                // Add the income to the array
                $monthlyIncome[] = $income;
            }

            // Return JSON response with the monthly income
            return response()->json([
                'status' => true,
                'message' => 'Monthly income retrieved successfully',
                'data' => [
                    'name' => 'Rs',
                    'data' => $monthlyIncome
                ]
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
