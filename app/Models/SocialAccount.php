<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'page_id',
        'page_name',
        'access_token',
        'user_token',
        'user_token_expires_at',
        'instagram_account_id',
        'instagram_username',
        'instagram_name',
        'status',
        'needs_reconnect',
        'connected_at',
    ];

    protected $hidden = ['access_token', 'user_token'];

    protected $casts = [
        'needs_reconnect' => 'boolean',
        'connected_at' => 'datetime',
        'user_token_expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'user_token' => 'encrypted',
    ];

    public function isFacebookConnected(): bool
    {
        return $this->status === 'connected' && ! $this->needs_reconnect;
    }

    public function isInstagramConnected(): bool
    {
        return $this->isFacebookConnected()
            && filled($this->instagram_account_id);
    }
}
