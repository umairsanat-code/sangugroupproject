<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\Social\FacebookService;
use App\Services\Social\InstagramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SocialMediaController extends Controller
{
    public function __construct(
        protected FacebookService $facebook,
        protected InstagramService $instagram
    ) {
    }

    /* ------------------------------------------------------------------ */
    /* Connections                                                        */
    /* ------------------------------------------------------------------ */

    public function accounts()
    {
        return response()->json([
            'configured' => (bool) (config('services.facebook.client_id') && config('services.facebook.client_secret')),
            'accounts' => collect(SocialAccount::orderBy('page_name')->get())->map(fn ($a) => [
                'id' => $a->id,
                'page_id' => $a->page_id,
                'page_name' => $a->page_name,
                'status' => $a->status,
                'needs_reconnect' => $a->needs_reconnect,
                'connected_at' => optional($a->connected_at)->toDateTimeString(),
                'user_token_expires_at' => optional($a->user_token_expires_at)->toDateTimeString(),
                'instagram' => $a->instagram_account_id ? [
                    'id' => $a->instagram_account_id,
                    'username' => $a->instagram_username,
                    'name' => $a->instagram_name,
                ] : null,
            ]),
        ]);
    }

    public function oauthUrl()
    {
        if (! config('services.facebook.client_id') || ! config('services.facebook.client_secret')) {
            return response()->json([
                'message' => 'Facebook App credentials are not configured. Add FACEBOOK_CLIENT_ID and FACEBOOK_CLIENT_SECRET to .env.',
            ], 422);
        }

        $state = bin2hex(random_bytes(16));
        Cache::put('social_oauth_state:' . $state, true, now()->addMinutes(10));

        return response()->json([
            'url' => $this->facebook->oauthRedirectUrl($state),
        ]);
    }

    /**
     * Facebook OAuth callback — browser lands here directly from Facebook.
     * No auth middleware: state parameter protects against CSRF.
     */
    public function oauthCallback(Request $request)
    {
        $redirect = rtrim(config('app.url'), '/') . '/admin/social-media';

        if ($request->filled('error')) {
            return redirect($redirect . '?fb_oauth=error&message=' . urlencode($request->query('error_description', 'Authorization cancelled')));
        }

        if (! $request->filled('code') || ! $request->filled('state')) {
            return redirect($redirect . '?fb_oauth=error&message=' . urlencode('Missing code or state from Facebook.'));
        }

        if (! Cache::pull('social_oauth_state:' . $request->query('state'))) {
            return redirect($redirect . '?fb_oauth=error&message=' . urlencode('Invalid or expired OAuth state.'));
        }

        try {
            $shortLived = $this->facebook->exchangeCodeForToken($request->query('code'));
            $longLived = $this->facebook->extendToLongLived($shortLived);
            $pages = $this->facebook->fetchUserPages($longLived['token']);
        } catch (\Throwable $e) {
            return redirect($redirect . '?fb_oauth=error&message=' . urlencode($e->getMessage()));
        }

        if (! $pages) {
            return redirect($redirect . '?fb_oauth=error&message=' . urlencode('No Facebook Pages found for this account. Create a Page first.'));
        }

        foreach ($pages as $page) {
            SocialAccount::updateOrCreate(
                ['page_id' => $page['id']],
                [
                    'page_name' => $page['name'],
                    'access_token' => $page['access_token'],
                    'user_token' => $longLived['token'],
                    'user_token_expires_at' => $longLived['expires_at'],
                    'instagram_account_id' => $page['instagram_business_account']['id'] ?? null,
                    'instagram_username' => $page['instagram_business_account']['username'] ?? null,
                    'instagram_name' => $page['instagram_business_account']['name'] ?? null,
                    'needs_reconnect' => false,
                ]
            );
        }

        return redirect($redirect . '?fb_oauth=success');
    }

    public function connectAccount(Request $request, $id)
    {
        $account = SocialAccount::findOrFail($id);
        $account->update([
            'status' => 'connected',
            'connected_at' => now(),
            'needs_reconnect' => false,
        ]);

        SocialAccount::where('status', 'discovered')->delete();

        return response()->json(['success' => true, 'account' => $account->fresh()]);
    }

    public function disconnectAccount($id)
    {
        $account = SocialAccount::findOrFail($id);
        $account->update(['status' => 'disconnected']);

        return response()->json(['success' => true]);
    }

    public function destroyAccount($id)
    {
        SocialAccount::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    /* ------------------------------------------------------------------ */
    /* Posts                                                              */
    /* ------------------------------------------------------------------ */

    public function store(Request $request)
    {
        $data = $this->validatePost($request);

        $media = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $stored = $file->store('public/social-posts');
                $media[] = [
                    'path' => str_replace('public/', '', $stored),
                    'type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                    'original_name' => $file->getClientOriginalName(),
                ];
            }
        }

        $targets = $data['targets'];
        $scheduledAt = ! empty($data['scheduled_at']) ? \Carbon\Carbon::parse($data['scheduled_at']) : null;

        $post = SocialPost::create([
            'caption' => $data['caption'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'media' => $media ?: null,
            'targets' => $targets,
            'facebook_status' => in_array('facebook', $targets, true) ? 'pending' : 'skipped',
            'instagram_status' => in_array('instagram', $targets, true) ? 'pending' : 'skipped',
            'scheduled_at' => $scheduledAt,
        ]);

        if ($scheduledAt && $scheduledAt->isFuture()) {
            return response()->json([
                'success' => true,
                'scheduled' => true,
                'message' => 'Post scheduled for ' . $scheduledAt->format('d M Y, h:i A'),
                'post' => $post,
            ], 201);
        }

        $post = $this->publishNow($post);

        return response()->json(['success' => true, 'scheduled' => false, 'post' => $post], 201);
    }

    protected function validatePost(Request $request): array
    {
        $data = $request->validate([
            'caption' => 'nullable|string|max:2200',
            'link_url' => 'nullable|url|max:1000',
            'targets' => 'required|array|min:1',
            'targets.*' => 'in:facebook,instagram',
            'media' => 'nullable|array|max:10',
            'media.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov|max:51200',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if (empty($data['caption']) && empty($data['link_url']) && ! $request->hasFile('media')) {
            abort(422, 'Provide a caption, media or a link.');
        }

        $files = $request->file('media') ?? [];
        $videos = array_filter($files, fn ($f) => str_starts_with($f->getMimeType(), 'video/'));
        $images = array_filter($files, fn ($f) => ! str_starts_with($f->getMimeType(), 'video/'));

        if (count($videos) > 1) {
            abort(422, 'Only one video per post is supported.');
        }
        if (count($videos) && count($images)) {
            abort(422, 'Mixing images and video in one post is not supported.');
        }
        if (in_array('facebook', $data['targets'], true) && count($images) > 1) {
            abort(422, 'Multiple images are supported on Facebook; keep it to one video or multiple images.');
        }

        return $data;
    }

    protected function publishNow(SocialPost $post): SocialPost
    {
        $account = SocialAccount::where('status', 'connected')->orderByDesc('connected_at')->first();

        foreach (['facebook', 'instagram'] as $platform) {
            if (! in_array($platform, $post->targets ?? [], true)) {
                continue;
            }

            if (! $account) {
                $post->{"{$platform}_status"} = 'failed';
                $post->{"{$platform}_error"} = 'No connected account. Connect a Facebook Page first.';

                continue;
            }

            $result = $platform === 'facebook'
                ? $this->facebook->publish($account, $post)
                : $this->instagram->publish($account, $post);

            $post->{"{$platform}_status"} = $result['success'] ? 'published' : 'failed';
            $post->{"{$platform}_post_id"} = $result['id'];
            $post->{"{$platform}_url"} = $result['url'];
            $post->{"{$platform}_error"} = $result['error'];

            if ($platform === 'facebook' && str_contains(strtolower((string) $result['error']), 'session has been invalidated')) {
                $account->update(['needs_reconnect' => true]);
            }
        }

        $post->published_at = now();
        $post->save();

        return $post->fresh();
    }

    public function posts()
    {
        return response()->json(SocialPost::orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => $this->presentPost($p)));
    }

    public function publishScheduled($id)
    {
        $post = SocialPost::where('id', $id)->whereNotNull('scheduled_at')->firstOrFail();

        if ($post->overallStatus() === 'pending') {
            $post = $this->publishNow($post);
        }

        return response()->json(['success' => true, 'post' => $this->presentPost($post)]);
    }

    public function destroyPost($id)
    {
        $post = SocialPost::findOrFail($id);

        foreach ($post->media ?? [] as $item) {
            Storage::disk('public')->delete($item['path'] ?? '');
        }

        $post->delete();

        return response()->json(['success' => true]);
    }

    protected function presentPost(SocialPost $p): array
    {
        return [
            'id' => $p->id,
            'caption' => $p->caption,
            'link_url' => $p->link_url,
            'media' => collect($p->media ?? [])->map(fn ($m) => [
                'type' => $m['type'] ?? 'image',
                'url' => isset($m['path']) ? asset('storage/' . ltrim($m['path'], '/')) : null,
            ]),
            'targets' => $p->targets,
            'facebook_status' => $p->facebook_status,
            'facebook_error' => $p->facebook_error,
            'facebook_url' => $p->facebook_url,
            'instagram_status' => $p->instagram_status,
            'instagram_error' => $p->instagram_error,
            'instagram_url' => $p->instagram_url,
            'scheduled_at' => optional($p->scheduled_at)->format('d M Y, h:i A'),
            'published_at' => optional($p->published_at)->format('d M Y, h:i A'),
            'created_at' => optional($p->created_at)->format('d M Y, h:i A'),
            'overall_status' => $p->overallStatus(),
        ];
    }
}
