<?php

namespace App\Services;

use App\Exceptions\ChatbotException;
use App\Models\ChatbotSetting;
use App\Models\ChatConversation;
use App\Models\ChatLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Orchestrates an end-to-end chatbot turn:
 *   load/create conversation -> persist the visitor message -> build the prompt
 *   from admin config -> call OpenAI (with optional tool-calling for lead
 *   capture) -> persist the assistant reply.
 *
 * All database access is wrapped defensively so that a not-yet-migrated
 * database cannot take the public site down.
 */
class ChatbotService
{
    public const BRAND = 'SanguGroup';
    public const PHONE = '+923085092631';
    public const EMAIL = 'sangutravel@gmail.com';

    /** Hard cap on how many past messages are replayed to the model. */
    protected const HISTORY_LIMIT = 20;

    public function settings(): ChatbotSetting
    {
        return ChatbotSetting::query()->firstOrCreate(['id' => 1]);
    }

    public function isEnabled(): bool
    {
        try {
            return (bool) $this->settings()->enabled;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Safe public configuration for the frontend widget. Contains NO secrets.
     */
    public function publicConfig(): array
    {
        try {
            $settings = $this->settings();
        } catch (\Throwable $e) {
            return [
                'enabled' => false,
                'title' => 'AI Assistant',
                'welcome_message' => 'Sorry, our assistant is unavailable right now.',
                'suggested_questions' => [],
            ];
        }

        return [
            'enabled' => (bool) $settings->enabled,
            'title' => 'Sangu AI Assistant',
            'welcome_message' => $settings->welcome_message
                ?: 'Assalamu Alaikum! Welcome to SanguGroup. How can we help with your travel plans today?',
            'suggested_questions' => array_values($settings->suggested_questions ?: []),
            'brand' => self::BRAND,
        ];
    }

    /**
     * Process a single visitor message and return the assistant reply.
     *
     * @return array{
     *   conversation_token: string,
     *   reply: string,
     *   error: bool,
     *   disabled: bool,
     *   log_id: int,
     *   lead_captured: bool
     * }
     */
    public function handleMessage(string $message, ?string $token, Request $request): array
    {
        $settings = $this->settings();
        $conversation = $this->findOrCreateConversation($token, $request);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $message,
        ]);
        $this->touch($conversation);

        // Chatbot disabled in admin settings.
        if (! $settings->enabled) {
            $reply = 'Our AI assistant is currently turned off. Please reach us at ' . self::EMAIL
                . ' or call ' . self::PHONE . ' and our team will be glad to help.';
            $this->storeReply($conversation, $reply, ['disabled' => true]);

            return $this->result($conversation, $reply, ['disabled' => true, 'error' => true]);
        }

        $openAi = new OpenAiService($settings);

        // No API key configured anywhere: never crash, return a friendly note.
        if (! $openAi->isConfigured()) {
            $reply = 'Our AI assistant is not available right now. Please contact us at '
                . self::EMAIL . ' or ' . self::PHONE . ' and we will assist you personally.';
            $this->storeReply($conversation, $reply, ['not_configured' => true]);

            return $this->result($conversation, $reply, ['disabled' => true, 'error' => true]);
        }

        try {
            [$reply, $leadCaptured, $usage] = $this->generateReply($settings, $openAi, $conversation);
        } catch (ChatbotException $e) {
            // Mark the failed user turn so the transcript stays honest.
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $e->getMessage(),
                'meta' => ['error' => true],
            ]);
            $this->touch($conversation);

            return $this->result($conversation, $e->getMessage(), [
                'error' => true,
                'error_message' => $e->getMessage(),
            ]);
        }

        $this->storeReply($conversation, $reply, [
            'lead_captured' => $leadCaptured,
        ], $usage);

        return $this->result($conversation, $reply, [
            'lead_captured' => $leadCaptured,
        ]);
    }

    /**
     * @return array{0: string, 1: bool, 2: array}
     */
    protected function generateReply(ChatbotSetting $settings, OpenAiService $openAi, ChatConversation $conversation): array
    {
        $messages = $this->buildMessages($settings, $conversation);
        $tools = $settings->lead_capture_enabled ? [$this->leadTool()] : [];

        $first = $openAi->chat($messages, $tools);
        $reply = trim((string) ($first['content'] ?? ''));
        $usage = $first['usage'] ?? [];
        $leadCaptured = false;

        if (! empty($first['tool_calls'])) {
            // Replay the assistant's tool request, then the tool results.
            $messages[] = [
                'role' => 'assistant',
                'content' => $first['content'],
                'tool_calls' => $first['tool_calls'],
            ];

            foreach ($first['tool_calls'] as $call) {
                $name = $call['function']['name'] ?? '';
                $args = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];

                $toolResult = $name === 'save_customer_inquiry'
                    ? $this->handleLeadTool($conversation, $args)
                    : ['saved' => false, 'message' => 'Unknown tool ' . $name];

                if (! empty($toolResult['saved'])) {
                    $leadCaptured = true;
                }

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $call['id'] ?? '',
                    'content' => json_encode($toolResult),
                ];
            }

            // Ask the model for its final, natural-language reply.
            $second = $openAi->chat($messages, $tools);
            $final = trim((string) ($second['content'] ?? ''));
            $reply = $final !== '' ? $final : $reply;
            $usage = $second['usage'] ?? $usage;
        }

        if ($reply === '') {
            $reply = $leadCaptured
                ? 'Thank you! I have noted your details and our team will contact you shortly.'
                : 'Sorry, I could not generate a response just now. Could you please rephrase your question?';
        }

        return [$reply, $leadCaptured, $usage];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array{saved: bool, message: string}
     */
    protected function handleLeadTool(ChatConversation $conversation, array $args): array
    {
        $consent = filter_var($args['consent'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $consent) {
            return [
                'saved' => false,
                'message' => 'Not saved: the visitor has NOT consented to being contacted. Politely ask for permission to be contacted and only save if they agree.',
            ];
        }

        $lead = ChatLead::create([
            'conversation_id' => $conversation->id,
            'name' => $this->clean($args['name'] ?? null, 255),
            'email' => $this->clean($args['email'] ?? null, 255),
            'phone' => $this->clean($args['phone'] ?? null, 255),
            'service_interest' => $this->clean($args['service_interest'] ?? null, 255),
            'message' => $this->clean($args['message'] ?? null, 2000),
            'status' => 'new',
            'consent' => true,
            'source' => 'chatbot',
        ]);

        $conversation->lead_id = $lead->id;
        $conversation->save();

        Log::info('Chatbot lead captured', ['lead_id' => $lead->id, 'conversation_id' => $conversation->id]);

        return [
            'saved' => true,
            'message' => 'Inquiry saved successfully. Let the visitor know warmly that our team will contact them soon.',
        ];
    }

    protected function buildMessages(ChatbotSetting $settings, ChatConversation $conversation): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt($settings)],
        ];

        $history = $conversation->messages()
            ->whereIn('role', ['user', 'assistant'])
            ->latest('id')
            ->limit(self::HISTORY_LIMIT)
            ->get()
            ->reverse();

        foreach ($history as $entry) {
            $content = trim((string) $entry->content);
            if ($content === '') {
                continue;
            }
            $messages[] = ['role' => $entry->role, 'content' => $content];
        }

        return $messages;
    }

    protected function buildSystemPrompt(ChatbotSetting $settings): string
    {
        $prompt = trim((string) ($settings->system_prompt
            ?: 'You are a helpful AI travel assistant for ' . self::BRAND . '.'));

        $services = $settings->services ?: [];
        if ($services) {
            $lines = [];
            foreach ($services as $service) {
                if (is_array($service)) {
                    $name = trim((string) ($service['name'] ?? ''));
                    $desc = trim((string) ($service['description'] ?? ''));
                    if ($name !== '') {
                        $lines[] = '- ' . $name . ($desc !== '' ? ': ' . $desc : '');
                    }
                } elseif (is_string($service) && trim($service) !== '') {
                    $lines[] = '- ' . trim($service);
                }
            }
            if ($lines) {
                $prompt .= "\n\nServices SanguGroup offers:\n" . implode("\n", $lines);
            }
        }

        $faqs = $settings->faqs ?: [];
        if ($faqs) {
            $lines = [];
            foreach ($faqs as $faq) {
                $q = trim((string) ($faq['question'] ?? ''));
                $a = trim((string) ($faq['answer'] ?? ''));
                if ($q !== '' && $a !== '') {
                    $lines[] = 'Q: ' . $q . "\nA: " . $a;
                }
            }
            if ($lines) {
                $prompt .= "\n\nFrequently asked questions:\n" . implode("\n", $lines);
            }
        }

        $prompt .= "\n\nContact details: phone " . self::PHONE . ', email ' . self::EMAIL . ', website ' . url('/') . '.';

        if ($settings->lead_capture_enabled) {
            $prompt .= "\n\nLead capture: When a visitor shows interest in any service (a quote, booking, or a callback), "
                . 'politely collect their name and a way to reach them (phone or email), along with the service they need. '
                . 'Then explicitly ask whether they agree to be contacted. '
                . 'Only after they clearly agree, call the save_customer_inquiry function with consent=true. '
                . 'Never call that function without the visitor\'s consent, and never make up details. '
                . 'After saving, warmly confirm that our team will reach out soon.';
        }

        return $prompt;
    }

    protected function leadTool(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => 'save_customer_inquiry',
                'description' => 'Save a customer inquiry/lead once the visitor has given explicit consent to be contacted. '
                    . 'Only call this after the visitor clearly agrees.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'The visitor\'s full name.'],
                        'email' => ['type' => 'string', 'description' => 'The visitor\'s email address, if provided.'],
                        'phone' => ['type' => 'string', 'description' => 'The visitor\'s phone/WhatsApp number, if provided.'],
                        'service_interest' => ['type' => 'string', 'description' => 'The service the visitor is interested in (e.g. Umrah package, visa, flight).'],
                        'message' => ['type' => 'string', 'description' => 'A short summary of the visitor\'s request.'],
                        'consent' => ['type' => 'boolean', 'description' => 'True only if the visitor explicitly agreed to be contacted.'],
                    ],
                    'required' => ['name', 'consent'],
                ],
            ],
        ];
    }

    protected function findOrCreateConversation(?string $token, Request $request): ChatConversation
    {
        if (is_string($token) && $token !== '') {
            $existing = ChatConversation::query()->where('uuid', $token)->first();
            if ($existing) {
                return $existing;
            }
        }

        return ChatConversation::create([
            'session_id' => null,
            'visitor_ip' => (string) $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'page_url' => Str::limit((string) $request->input('page_url', $request->headers->get('referer')), 500, ''),
            'status' => 'open',
            'last_activity_at' => now(),
        ]);
    }

    protected function storeReply(ChatConversation $conversation, string $reply, array $meta = [], array $usage = []): void
    {
        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $reply,
            'tokens' => $usage['completion_tokens'] ?? null,
            'meta' => $meta ?: null,
        ]);
        $this->touch($conversation);
    }

    protected function touch(ChatConversation $conversation): void
    {
        $conversation->forceFill(['last_activity_at' => now()])->save();
    }

    protected function result(ChatConversation $conversation, string $reply, array $extra = []): array
    {
        return array_merge([
            'conversation_token' => $conversation->uuid,
            'reply' => $reply,
            'error' => false,
            'disabled' => false,
            'lead_captured' => false,
        ], $extra);
    }

    protected function clean($value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));

        return $value === '' ? null : Str::limit($value, $max, '');
    }
}
