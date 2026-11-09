<?php

namespace App\Services;

use App\Exceptions\ChatbotException;
use App\Models\ChatbotSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the official OpenAI Chat Completions API.
 *
 * The API key is resolved from the admin-configured (encrypted) setting first,
 * then falls back to the server-side environment variable. It is only ever used
 * here on the server and is never returned to the browser.
 */
class OpenAiService
{
    protected ChatbotSetting $settings;

    public function __construct(?ChatbotSetting $settings = null)
    {
        $this->settings = $settings ?: ChatbotSetting::query()->first() ?: new ChatbotSetting();
    }

    public function apiKey(): ?string
    {
        $key = trim((string) ($this->settings->api_key ?? ''));

        if ($key !== '') {
            return $key;
        }

        $envKey = trim((string) config('services.openai.key', ''));

        return $envKey !== '' ? $envKey : null;
    }

    public function isConfigured(): bool
    {
        return $this->apiKey() !== null;
    }

    public function model(): string
    {
        return (string) ($this->settings->model ?: config('services.openai.model', 'gpt-4o-mini'));
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
    }

    /**
     * Send a chat completion request.
     *
     * @param  array<int, array<string, mixed>>  $messages  OpenAI-format messages.
     * @param  array<int, array<string, mixed>>  $tools     Optional tool/function definitions.
     * @return array{content: ?string, tool_calls: array, usage: array, finish_reason: ?string}
     *
     * @throws ChatbotException
     */
    public function chat(array $messages, array $tools = []): array
    {
        $key = $this->apiKey();
        if ($key === null) {
            throw ChatbotException::notConfigured();
        }

        $payload = [
            'model' => $this->model(),
            'messages' => $messages,
            'temperature' => (float) ($this->settings->temperature ?? 0.4),
            'max_tokens' => (int) ($this->settings->max_tokens ?: 500),
        ];

        if (! empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        try {
            $response = Http::withToken($key)
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.openai.timeout', 60))
                ->post($this->baseUrl() . '/chat/completions', $payload);
        } catch (\Throwable $e) {
            Log::error('OpenAI request failed (connection): ' . $e->getMessage());

            throw ChatbotException::api('We could not reach the AI service just now. Please try again in a moment.');
        }

        if ($response->failed()) {
            $error = $response->json('error.message') ?: ('HTTP ' . $response->status());
            Log::error('OpenAI API error: ' . $error, ['status' => $response->status()]);

            throw ChatbotException::api('The AI assistant is temporarily unavailable. Please try again shortly.');
        }

        $data = $response->json();
        $message = $data['choices'][0]['message'] ?? [];

        return [
            'content' => $message['content'] ?? null,
            'tool_calls' => $message['tool_calls'] ?? [],
            'usage' => $data['usage'] ?? [],
            'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
        ];
    }
}
