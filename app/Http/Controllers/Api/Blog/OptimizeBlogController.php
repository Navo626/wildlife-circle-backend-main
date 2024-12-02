<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\OptimizeBlogRequest;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class OptimizeBlogController extends Controller
{
    /**
     * Correct the grammar of the provided text
     *
     * @param OptimizeBlogRequest $request
     * @return JsonResponse
     */
    public function optimize(OptimizeBlogRequest $request): JsonResponse
    {
        try {
            // Rate Limiting
            $key = $request->user()->id . '|' . $request->ip();
            $maxAttempts = 1;
            $decaySeconds = 30;

            // Check if the user has exceeded the maximum number of attempts
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'status' => false,
                    'message' => 'Too many attempts. Try again in ' . $seconds . ' seconds.',
                ], 429);
            }

            $client = new Client();
            $apiKey = env('GOOGLE_GEMINI_API_KEY');
            $url = env('GOOGLE_GEMINI_API_URL') . '?key=' . $apiKey;
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Correct the grammar of the following text and provide only the corrected text. Do not add any comments or extra information:\n\n" . $request->input('body'),
                            ]
                        ]
                    ]
                ]
            ];

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($response->getBody(), true);

            $correctedText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? $request->input('text');

            // Increase rate limiter
            RateLimiter::hit($key, $decaySeconds);

            return response()->json([
                'status' => true,
                'message' => 'Optimization successful',
                'data' => $correctedText
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
