<?php

namespace App\Services\Social;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

abstract class GraphApiService
{
    protected function apiBase(): string
    {
        return 'https://graph.facebook.com/' . config('services.facebook.graph_version', 'v21.0');
    }

    protected function get(string $path, array $query = []): array
    {
        $response = Http::get($this->apiBase() . $path, $query)->throw()->json();

        return $response ?? [];
    }

    protected function postForm(string $path, array $form): array
    {
        return Http::asJson()->post($this->apiBase() . $path, $form)->throw()->json() ?? [];
    }

    /**
     * Upload a local file as multipart binary payload (works without public URL).
     */
    protected function postWithFile(string $path, string $fileField, string $absolutePath, array $form, string $token): array
    {
        $response = Http::attach(
            $fileField,
            fopen($absolutePath, 'r'),
            basename($absolutePath)
        )->post($this->apiBase() . $path . '?access_token=' . urlencode($token), $form);

        if ($response->failed()) {
            $this->throwGraphError($response);
        }

        return $response->json() ?? [];
    }

    protected function throwGraphError($response): never
    {
        $payload = $response->json();
        $message = $payload['error']['message'] ?? 'Unknown Graph API error (HTTP ' . $response->status() . ')';

        throw new \RuntimeException($message);
    }

    protected function absoluteMediaUrl(string $storagePath): string
    {
        $url = rtrim(config('app.url'), '/') . '/storage/' . ltrim($storagePath, '/');

        if (! str_starts_with($url, 'https://')) {
            throw new \RuntimeException(
                "Instagram requires media on a public HTTPS URL. Set APP_URL in .env to your public HTTPS domain (currently: {$url})."
            );
        }

        return $url;
    }

    protected function localPath(?string $storagePath): ?string
    {
        if (! $storagePath) {
            return null;
        }

        $path = Storage::disk('public')->path($storagePath);

        return file_exists($path) ? $path : null;
    }

    protected function assertTokenValid(SocialAccount $account): void
    {
        if ($account->needs_reconnect || $account->status !== 'connected') {
            throw new \RuntimeException("Page '{$account->page_name}' is not connected. Reconnect it from Social Media → Connections.");
        }
    }
}
