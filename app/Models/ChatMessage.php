<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tokens',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'tokens' => 'integer',
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }
}
