<?php

namespace App\Services\Social;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class InstagramService extends GraphApiService
{
    protected const STATUS_POLL_TRIES = 30;
    protected const STATUS_POLL_SLEEP = 4;

    /**
     * Publish a post to the connected Instagram Business account.
     *
     * @return array{success: bool, id: ?string, url: ?string, error: ?string}
     */
    public function publish(SocialAccount $account, SocialPost $post): array
    {
        try {
            $this->assertTokenValid($account);

            if (! $account->instagram_account_id) {
                throw new RuntimeException("No Instagram Business account is linked to Facebook Page '{$account->page_name}'.");
            }

            $igId = $account->instagram_account_id;
            $token = $account->access_token;
            $caption = mb_substr((string) ($post->caption ?? ''), 0, 2200);
            $images = $post->mediaOfType('image');
            $videos = $post->mediaOfType('video');

            if ($videos) {
                return $this->publishVideo($igId, $token, $caption, $videos[0]['path']);
            }

            if (count($images) > 1) {
                return $this->publishCarousel($igId, $token, $caption, $images);
            }

            if ($images) {
                return $this->publishSingleImage($igId, $token, $caption, $images[0]['path']);
            }

            throw new RuntimeException('Instagram requires at least one image or video to publish. Link-only posts are not supported.');
        } catch (\Throwable $e) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $e->getMessage()];
        }
    }

    protected function publishSingleImage(string $igId, string $token, string $caption, string $storagePath): array
    {
        $imageUrl = $this->absoluteMediaUrl($storagePath);

        $container = Http::asJson()->withToken($token)
            ->post("{$this->apiBase()}/{$igId}/media", [
                'image_url' => $imageUrl,
                'caption' => $caption,
            ]);

        if ($container->failed()) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $this->errorMessage($container)];
        }

        return $this->publishContainer($igId, $token, $container->json('id'));
    }

    protected function publishCarousel(string $igId, string $token, string $caption, array $images): array
    {
        $children = [];
        foreach ($images as $image) {
            $child = Http::asJson()->withToken($token)
                ->post("{$this->apiBase()}/{$igId}/media", [
                    'image_url' => $this->absoluteMediaUrl($image['path']),
                    'is_carousel_item' => 'true',
                ]);

            if ($child->failed()) {
                return ['success' => false, 'id' => null, 'url' => null, 'error' => $this->errorMessage($child)];
            }

            $children[] = $child->json('id');
        }

        $parent = Http::asJson()->withToken($token)
            ->post("{$this->apiBase()}/{$igId}/media", [
                'media_type' => 'CAROUSEL',
                'children' => implode(',', $children),
                'caption' => $caption,
            ]);

        if ($parent->failed()) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $this->errorMessage($parent)];
        }

        return $this->publishContainer($igId, $token, $parent->json('id'));
    }

    protected function publishVideo(string $igId, string $token, string $caption, string $storagePath): array
    {
        $videoUrl = $this->absoluteMediaUrl($storagePath);

        $container = Http::asJson()->withToken($token)
            ->post("{$this->apiBase()}/{$igId}/media", [
                'media_type' => 'REELS',
                'video_url' => $videoUrl,
                'caption' => $caption,
            ]);

        if ($container->failed()) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $this->errorMessage($container)];
        }

        $this->waitForContainer($igId, $token, $container->json('id'));

        return $this->publishContainer($igId, $token, $container->json('id'));
    }

    protected function waitForContainer(string $igId, string $token, string $containerId): void
    {
        for ($i = 0; $i < self::STATUS_POLL_TRIES; $i++) {
            sleep(self::STATUS_POLL_SLEEP);

            $status = Http::get("{$this->apiBase()}/{$containerId}", [
                'fields' => 'status_code,status',
                'access_token' => $token,
            ])->json();

            $code = $status['status_code'] ?? null;

            if ($code === 'FINISHED') {
                return;
            }

            if ($code === 'ERROR') {
                throw new RuntimeException('Instagram media processing failed: ' . ($status['status'] ?? 'unknown error'));
            }
        }

        throw new RuntimeException('Instagram media processing timed out. Try again with a smaller file.');
    }

    protected function publishContainer(string $igId, string $token, string $containerId): array
    {
        $published = Http::asJson()->withToken($token)
            ->post("{$this->apiBase()}/{$igId}/media_publish", [
                'creation_id' => $containerId,
            ]);

        if ($published->failed()) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $this->errorMessage($published)];
        }

        $id = $published->json('id');
        $url = null;

        if ($id) {
            $meta = Http::get("{$this->apiBase()}/{$id}", [
                'fields' => 'permalink',
                'access_token' => $token,
            ])->json();
            $url = $meta['permalink'] ?? null;
        }

        return ['success' => true, 'id' => $id, 'url' => $url, 'error' => null];
    }

    protected function errorMessage($response): string
    {
        $payload = $response->json();

        return $payload['error']['message'] ?? 'Instagram API error (HTTP ' . $response->status() . ')';
    }
}
