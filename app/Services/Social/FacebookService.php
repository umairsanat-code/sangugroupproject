<?php

namespace App\Services\Social;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FacebookService extends GraphApiService
{
    /**
     * Publish a post to the connected Facebook Page.
     *
     * @return array{success: bool, id: ?string, url: ?string, error: ?string}
     */
    public function publish(SocialAccount $account, SocialPost $post): array
    {
        try {
            $this->assertTokenValid($account);

            $token = $account->access_token;
            $pageId = $account->page_id;
            $images = $post->mediaOfType('image');
            $videos = $post->mediaOfType('video');
            $caption = (string) ($post->caption ?? '');

            if ($videos) {
                return $this->publishVideo($pageId, $token, $caption, $videos[0]['path']);
            }

            if ($images) {
                return $this->publishPhotos($pageId, $token, $caption, $images);
            }

            return $this->publishFeed($pageId, $token, $caption, $post->link_url);
        } catch (\Throwable $e) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $e->getMessage()];
        }
    }

    protected function publishFeed(string $pageId, string $token, string $message, ?string $link): array
    {
        $form = [];
        if ($message !== '') {
            $form['message'] = $message;
        }
        if ($link) {
            $form['link'] = $link;
        }

        if (! $form) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => 'Nothing to publish: provide caption, media or link.'];
        }

        $result = Http::asJson()->withToken($token)
            ->post("{$this->apiBase()}/{$pageId}/feed", $form);

        if ($result->failed()) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $this->errorMessage($result)];
        }

        $id = $result->json('id');

        return [
            'success' => true,
            'id' => $id,
            'url' => $this->postUrl($pageId, $id),
            'error' => null,
        ];
    }

    protected function publishPhotos(string $pageId, string $token, string $caption, array $images): array
    {
        if (count($images) === 1) {
            $path = $this->localPath($images[0]['path']);
            if (! $path) {
                return ['success' => false, 'id' => null, 'url' => null, 'error' => 'Uploaded image file is missing on the server.'];
            }

            $result = $this->postWithFile("{$pageId}/photos", 'source', $path, [
                'published' => 'true',
                'caption' => $caption,
            ], $token);

            $id = $result['post_id'] ?? $result['id'] ?? null;

            return ['success' => true, 'id' => $id, 'url' => $this->postUrl($pageId, $id), 'error' => null];
        }

        $mediaFbids = [];
        foreach (array_values($images) as $index => $image) {
            $path = $this->localPath($image['path'] ?? '');
            if (! $path) {
                continue;
            }

            $uploaded = $this->postWithFile("{$pageId}/photos", 'source', $path, [
                'published' => 'false',
            ], $token);

            if (! empty($uploaded['id'])) {
                $mediaFbids["attached_media[{$index}]"] = json_encode(['media_fbid' => $uploaded['id']]);
            }
        }

        if (! $mediaFbids) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => 'None of the images could be uploaded to Facebook.'];
        }

        $form = array_merge(['message' => $caption], $mediaFbids);
        $result = Http::asJson()->withToken($token)
            ->post("{$this->apiBase()}/{$pageId}/feed", $form);

        if ($result->failed()) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => $this->errorMessage($result)];
        }

        $id = $result->json('id');

        return ['success' => true, 'id' => $id, 'url' => $this->postUrl($pageId, $id), 'error' => null];
    }

    protected function publishVideo(string $pageId, string $token, string $caption, ?string $storagePath): array
    {
        $path = $this->localPath($storagePath);
        if (! $path) {
            return ['success' => false, 'id' => null, 'url' => null, 'error' => 'Uploaded video file is missing on the server.'];
        }

        set_time_limit(0);

        $result = $this->postWithFile("{$pageId}/videos", 'source', $path, [
            'description' => $caption,
        ], $token);

        $id = $result['id'] ?? null;

        return ['success' => true, 'id' => $id, 'url' => $this->postUrl($pageId, $id), 'error' => null];
    }

    protected function postUrl(string $pageId, ?string $postId): ?string
    {
        return $postId ? "https://www.facebook.com/{$postId}" : null;
    }

    protected function errorMessage($response): string
    {
        $payload = $response->json();

        return $payload['error']['message'] ?? 'Facebook API error (HTTP ' . $response->status() . ')';
    }

    /**
     * Fetch all pages (and their IG accounts) for the given long-lived user token.
     */
    public function fetchUserPages(string $userToken): array
    {
        $response = Http::get($this->apiBase() . '/me/accounts', [
            'access_token' => $userToken,
            'fields' => 'id,name,access_token,instagram_business_account{id,name,username}',
            'limit' => 100,
        ]);

        if ($response->failed()) {
            $this->throwGraphError($response);
        }

        return $response->json('data') ?? [];
    }

    public function exchangeCodeForToken(string $code): string
    {
        $response = Http::get('https://graph.facebook.com/oauth/access_token', [
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri' => $this->redirectUri(),
            'code' => $code,
        ]);

        if ($response->failed()) {
            $this->throwGraphError($response);
        }

        return $response->json('access_token');
    }

    public function extendToLongLived(string $shortLivedToken): array
    {
        $response = Http::get('https://graph.facebook.com/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'fb_exchange_token' => $shortLivedToken,
        ]);

        if ($response->failed()) {
            $this->throwGraphError($response);
        }

        $expiresIn = (int) $response->json('expires_in', 5184000);

        return [
            'token' => $response->json('access_token'),
            'expires_at' => now()->addSeconds($expiresIn),
        ];
    }

    public static function redirectUri(): string
    {
        return rtrim(config('app.url'), '/') . '/api/social/facebook/callback';
    }

    public function oauthRedirectUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => config('services.facebook.client_id'),
            'redirect_uri' => self::redirectUri(),
            'state' => $state,
            'response_type' => 'code',
            'scope' => implode(',', [
                'public_profile',
                'pages_show_list',
                'pages_manage_posts',
                'pages_read_engagement',
                'instagram_basic',
                'instagram_content_publish',
                'business_management',
            ]),
        ]);

        return 'https://www.facebook.com/' . config('services.facebook.graph_version', 'v21.0') . '/dialog/oauth?' . $query;
    }
}
