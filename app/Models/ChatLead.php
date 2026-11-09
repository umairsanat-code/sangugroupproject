<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLead extends Model
{
    protected $table = 'chat_leads';

    /** Lead pipeline statuses shown in the admin panel. */
    public const STATUSES = ['new', 'contacted', 'qualified', 'won', 'lost'];

    protected $fillable = [
        'conversation_id',
        'name',
        'email',
        'phone',
        'service_interest',
        'message',
        'status',
        'consent',
        'source',
        'notes',
    ];

    protected $casts = [
        'consent' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }
}
