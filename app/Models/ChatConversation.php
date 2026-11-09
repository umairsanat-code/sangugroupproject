<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatConversation extends Model
{
    protected $table = 'chat_conversations';

    protected $fillable = [
        'uuid',
        'session_id',
        'visitor_ip',
        'user_agent',
        'page_url',
        'status',
        'lead_id',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $conversation) {
            if (empty($conversation->uuid)) {
                $conversation->uuid = (string) Str::uuid();
            }
        });
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('id');
    }

    public function lead()
    {
        return $this->belongsTo(ChatLead::class, 'lead_id');
    }

    public function leads()
    {
        return $this->hasMany(ChatLead::class, 'conversation_id');
    }
}
