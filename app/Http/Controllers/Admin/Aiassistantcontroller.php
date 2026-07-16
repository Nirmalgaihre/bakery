<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;   // <-- this was missing, causing a 500 error
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function query(Request $request): JsonResponse
    {
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return response()->json(['reply' => 'DEBUG: GEMINI_API_KEY is empty in .env'], 200);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->post($url, [
                    'contents' => [['parts' => [['text' => $request->message]]]]
                ]);

            if ($response->failed()) {
                Log::warning('Gemini API failed', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['reply' => 'API FAILED (' . $response->status() . '): ' . $response->body()], 200);
            }

            $data = $response->json();
            $reply = data_get($data, 'candidates.0.content.parts.0.text', 'SUCCESS but no text found: ' . $response->body());

            return response()->json(['reply' => trim($reply)], 200);

        } catch (\Exception $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());
            return response()->json(['reply' => 'EXCEPTION: ' . $e->getMessage()], 200);
        }
    }
}