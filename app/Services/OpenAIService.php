<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    /**
     * Generate text or answers based on a prompt.
     */
    public function generateResponse(string $prompt, string $systemMessage = 'You are a helpful assistant.'): string
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o', // or gpt-4o-mini for faster/cheaper tasks
            'messages' => [
                ['role' => 'system', 'content' => $systemMessage],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
        ]);

        return $response->choices[0]->message->content;
    }
}