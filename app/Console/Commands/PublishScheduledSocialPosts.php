<?php

namespace App\Console\Commands;

use App\Http\Controllers\SocialMediaController;
use App\Models\SocialPost;
use Illuminate\Console\Command;

class PublishScheduledSocialPosts extends Command
{
    protected $signature = 'social:publish-scheduled';

    protected $description = 'Publish scheduled social media posts that are due';

    public function handle(SocialMediaController $controller): int
    {
        $due = SocialPost::whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get()
            ->filter(fn (SocialPost $post) => $post->overallStatus() === 'pending');

        foreach ($due as $post) {
            $controller->publishScheduled($post->id);
            $this->info("Published scheduled post #{$post->id}");
        }

        $this->info("Done. {$due->count()} post(s) processed.");

        return self::SUCCESS;
    }
}
