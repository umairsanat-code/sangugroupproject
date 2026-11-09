<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    protected $table = 'chatbot_settings';

    protected $fillable = [
        'enabled',
        'api_key',
        'model',
        'system_prompt',
        'welcome_message',
        'suggested_questions',
        'services',
        'faqs',
        'temperature',
        'max_tokens',
        'lead_capture_enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'lead_capture_enabled' => 'boolean',
        'suggested_questions' => 'array',
        'services' => 'array',
        'faqs' => 'array',
        // Encrypted at rest. Decrypted transparently when read on the server.
        'api_key' => 'encrypted',
        'temperature' => 'float',
        'max_tokens' => 'integer',
    ];

    /**
     * Never expose the API key in any JSON/array serialization (e.g. admin
     * responses, logs). Admin UIs only need to know whether a key is set.
     */
    protected $hidden = [
        'api_key',
    ];

    protected $appends = [
        'has_api_key',
    ];

    public function getHasApiKeyAttribute(): bool
    {
        return ! empty($this->attributes['api_key'] ?? null);
    }
}
