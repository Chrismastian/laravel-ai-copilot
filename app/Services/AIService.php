<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    protected ?string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.ninerouters.key');
        $this->baseUrl = rtrim(config('services.ninerouters.base_url') ?? '', '/');
        $this->model = config('services.ninerouters.model') ?? '';
    }

    public function chat(string $prompt, ?string $model = null): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('NINE_ROUTERS_API_KEY is not configured in .env');
        }

        if (empty($this->baseUrl)) {
            throw new \Exception('NINE_ROUTERS_BASE_URL is not configured in .env');
        }

        $model = $model ?: $this->model;

        if (empty($model)) {
            throw new \Exception('No model set. Fill NINE_ROUTERS_MODEL or send a "model" field in the request.');
        }

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
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
