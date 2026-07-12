<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Proxies chat questions from the admin "AI Insights" panel to Google's
 * Gemini API. The API key stays server-side (in .env) and is never
 * exposed to the browser.
 *
 * Setup:
 *   1. composer require guzzlehttp/guzzle   (already a Laravel dependency)
 *   2. Add to .env:      GEMINI_API_KEY=your_key_here
 *   3. Add to config/services.php:
 *          'gemini' => [
 *              'key' => env('GEMINI_API_KEY'),
 *          ],
 *   4. Register the route (routes/web.php, inside your admin/auth group):
 *          Route::post('/admin/ai/query', [AiAssistantController::class, 'query'])
 *              ->name('admin.ai.query');
 */
class AiAssistantController extends Controller
{
    public function query(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            return response()->json([
                'reply' => 'AI assistant is not configured yet. Please set GEMINI_API_KEY in the environment file.',
            ], 200);
        }

        // Optional: give the model light business context so answers are
        // grounded in this system rather than generic. Keep this cheap —
        // pull small summary numbers, not full table dumps.
        $context = $this->buildBusinessContext();

        $prompt = "You are an AI assistant embedded in Deurali Chemicals' inventory "
            . "and sales management system. Answer concisely and professionally. "
            . "Use the context below if relevant; if the answer isn't in the "
            . "context, say so rather than guessing.\n\n"
            . "CONTEXT:\n{$context}\n\n"
            . "QUESTION:\n{$validated['message']}";

        try {
            $response = Http::timeout(20)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey,
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                    ]
                );

            if (! $response->successful()) {
                Log::warning('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'reply' => 'The AI service is temporarily unavailable. Please try again shortly.',
                ], 200);
            }

            $data = $response->json();
            $reply = data_get($data, 'candidates.0.content.parts.0.text', 'I could not generate a response for that.');

            return response()->json(['reply' => trim($reply)]);
        } catch (\Throwable $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());

            return response()->json([
                'reply' => 'Something went wrong while contacting the AI assistant.',
            ], 200);
        }
    }

    /**
     * Pull a lightweight snapshot of business data to ground the model's
     * answers. Replace/extend with real model queries as needed — keep it
     * small (counts/sums), never dump full customer or financial records.
     */
    private function buildBusinessContext(): string
    {
        // Example (uncomment and adapt to your actual models):
        //
        // $lowStockCount = \App\Models\Product::where('initial_stock', '<=', 10)->count();
        // $todaySalesTotal = \App\Models\Sale::whereDate('created_at', today())->sum('total');
        // $duesoonCheques = \App\Models\Cheque::whereDate('due_date', today())->count();
        //
        // return "Low stock items: {$lowStockCount}\n"
        //      . "Today's sales total: {$todaySalesTotal}\n"
        //      . "Cheques due today: {$duesoonCheques}";

        return 'No live business context wired up yet — connect this method to your models.';
    }
}