<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $table = 'social_links';

    protected $fillable = [
        'facebook',
        'instagram',
        'whatsapp',
        'youtube',
        'tiktok',
        'telegram',
        'twitter',
    ];
}