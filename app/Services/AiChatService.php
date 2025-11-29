<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    protected $apiKey;
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        // Default to OpenAI, but can be configured
        $this->apiKey = env('OPENAI_API_KEY');
        $this->baseUrl = 'https://api.openai.com/v1/chat/completions';
        $this->model = 'gpt-4o-mini';
    }

    public function generateSentences(string $word, string $level = 'HSK 1')
    {
        $prompt = $this->buildPrompt($word, $level);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that generates Chinese sentences for HSK students. You must output valid JSON only.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return json_decode($content, true);
            }

            Log::error('OpenAI API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('AiChatService Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function buildPrompt($word, $level)
    {
        return <<<EOT
Generate 5 distinct example sentences in Chinese using the word "{$word}".
The sentences should be appropriate for {$level} level.
Provide the output in the following JSON format:
{
    "sentences": [
        {
            "sentence": "Chinese sentence here",
            "pinyin": "Pinyin here",
            "translation": "Thai translation here"
        }
    ]
}
Ensure the translation is natural in Thai.
EOT;
    }
}
