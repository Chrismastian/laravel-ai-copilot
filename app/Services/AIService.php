<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.ninerouters.key');
        $this->baseUrl = config('services.ninerouters.base_url', 'https://api.9routers.com/v1');
        $this->model = config('services.ninerouters.model', 'gpt-4o-mini');
    }

    public function chat(string $prompt): string
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception('AI API Error: ' . $response->body());
        }

        return $response->json('choices.0.message.content');
    }
}
