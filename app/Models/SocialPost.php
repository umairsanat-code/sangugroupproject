<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    protected $fillable = [
        'caption',
        'link_url',
        'media',
        'targets',
        'facebook_status',
        'facebook_post_id',
        'facebook_url',
        'facebook_error',
        'instagram_status',
        'instagram_post_id',
        'instagram_url',
        'instagram_error',
        'scheduled_at',
        'published_at',
    ];

    protected $casts = [
        'media' => 'array',
        'targets' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function mediaOfType(string $type): array
    {
        return collect($this->media ?? [])->filter(fn ($m) => ($m['type'] ?? '') === $type)->values()->all();
    }

    public function overallStatus(): string
    {
        $statuses = [$this->facebook_status, $this->instagram_status];

        if (in_array('pending', $statuses, true)) {
            return 'pending';
        }

        if (in_array('published', $statuses, true)) {
            return in_array('failed', $statuses, true) ? 'partial' : 'published';
        }

        return 'failed';
    }
}
