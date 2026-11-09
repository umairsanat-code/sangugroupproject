<?php

namespace Tests\Feature;

use App\Models\ChatbotSetting;
use App\Models\ChatConversation;
use App\Models\ChatLead;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // A dummy key so the AI path is exercised (tests never hit the network).
        config([
            'services.openai.key' => 'sk-test-key',
            'services.openai.base_url' => 'https://api.openai.com/v1',
        ]);
    }

    /** @return array<string, mixed> */
    private function openAiReply(string $content): array
    {
        return [
            'choices' => [[
                'message' => ['role' => 'assistant', 'content' => $content],
                'finish_reason' => 'stop',
            ]],
            'usage' => ['prompt_tokens' => 12, 'completion_tokens' => 6],
        ];
    }

    /** @return array<string, mixed> */
    private function openAiToolCall(string $name, array $args): array
    {
        return [
            'choices' => [[
                'message' => [
                    'role' => 'assistant',
                    'content' => null,
                    'tool_calls' => [[
                        'id' => 'call_abc',
                        'type' => 'function',
                        'function' => ['name' => $name, 'arguments' => json_encode($args)],
                    ]],
                ],
                'finish_reason' => 'tool_calls',
            ]],
            'usage' => ['prompt_tokens' => 30, 'completion_tokens' => 10],
        ];
    }

    public function test_public_config_is_returned_and_never_leaks_the_api_key(): void
    {
        $settings = ChatbotSetting::first();
        $settings->update(['api_key' => 'sk-secret-should-not-leak', 'enabled' => true]);

        $response = $this->getJson('/api/chat/config');

        $response->assertOk()
            ->assertJson(['enabled' => true])
            ->assertJsonStructure(['enabled', 'welcome_message', 'suggested_questions']);

        $this->assertArrayNotHasKey('api_key', $response->json());
        $this->assertStringNotContainsString('sk-secret-should-not-leak', $response->getContent());
    }

    public function test_first_message_gets_an_ai_reply_and_is_persisted(): void
    {
        Http::fake(['api.openai.com/*' => Http::response($this->openAiReply('Hello! How can I help you travel today?'))]);

        $response = $this->postJson('/api/chat/message', ['message' => 'Hi there']);

        $response->assertOk()
            ->assertJson(['error' => false])
            ->assertJsonPath('reply', 'Hello! How can I help you travel today?');

        $token = $response->json('conversation_token');
        $this->assertNotEmpty($token);

        $conversation = ChatConversation::where('uuid', $token)->first();
        $this->assertNotNull($conversation);
        $this->assertSame(2, $conversation->messages()->count());
        $this->assertDatabaseHas('chat_messages', ['conversation_id' => $conversation->id, 'role' => 'user', 'content' => 'Hi there']);
        $this->assertDatabaseHas('chat_messages', ['conversation_id' => $conversation->id, 'role' => 'assistant']);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/chat/completions'));
    }

    public function test_conversation_history_is_maintained_across_messages(): void
    {
        Http::fake(['api.openai.com/*' => Http::sequence()
            ->push($this->openAiReply('Welcome!'))
            ->push($this->openAiReply('Sure, our Umrah packages start from...'))]);

        $first = $this->postJson('/api/chat/message', ['message' => 'Hi'])->assertOk();
        $token = $first->json('conversation_token');

        $second = $this->postJson('/api/chat/message', [
            'message' => 'Tell me about Umrah packages',
            'conversation_token' => $token,
        ])->assertOk();

        $this->assertSame($token, $second->json('conversation_token'));

        $conversations = ChatConversation::where('uuid', $token)->get();
        $this->assertCount(1, $conversations, 'A new conversation must not be created for an existing token');
        $this->assertSame(4, $conversations->first()->messages()->count());

        // The second OpenAI call must have included the earlier turns.
        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), '/chat/completions')) {
                return false;
            }
            $messages = $request['messages'] ?? [];

            return collect($messages)->contains(fn ($m) => ($m['content'] ?? null) === 'Hi');
        });
    }

    public function test_lead_is_captured_via_tool_call_when_consent_is_given(): void
    {
        Http::fake(['api.openai.com/*' => Http::sequence()
            ->push($this->openAiToolCall('save_customer_inquiry', [
                'name' => 'Ali Khan',
                'phone' => '+923001234567',
                'service_interest' => 'Umrah package',
                'consent' => true,
            ]))
            ->push($this->openAiReply('Thank you Ali! Our team will contact you shortly.'))]);

        $response = $this->postJson('/api/chat/message', ['message' => 'Yes please call me, I am Ali, my number is 03001234567'])
            ->assertOk()
            ->assertJsonPath('lead_captured', true);

        $this->assertDatabaseHas('chat_leads', [
            'name' => 'Ali Khan',
            'phone' => '+923001234567',
            'service_interest' => 'Umrah package',
            'consent' => 1,
            'source' => 'chatbot',
        ]);

        $lead = ChatLead::first();
        $conversation = ChatConversation::where('uuid', $response->json('conversation_token'))->first();
        $this->assertSame($lead->id, $conversation->lead_id);
    }

    public function test_lead_is_not_captured_without_consent(): void
    {
        Http::fake(['api.openai.com/*' => Http::sequence()
            ->push($this->openAiToolCall('save_customer_inquiry', [
                'name' => 'Ali Khan',
                'phone' => '+923001234567',
                'consent' => false,
            ]))
            ->push($this->openAiReply('No problem! Let me know if anything changes.'))]);

        $this->postJson('/api/chat/message', ['message' => 'Here are my details but do not call'])
            ->assertOk()
            ->assertJsonPath('lead_captured', false);

        $this->assertSame(0, ChatLead::count());
    }

    public function test_friendly_message_when_chatbot_is_disabled(): void
    {
        Http::fake();
        ChatbotSetting::first()->update(['enabled' => false]);

        $response = $this->postJson('/api/chat/message', ['message' => 'Hello?'])->assertOk();

        $this->assertStringContainsString('turned off', strtolower($response->json('reply')));
        $this->assertTrue($response->json('error'));
        Http::assertNothingSent();
        $this->assertDatabaseCount('chat_messages', 2);
    }

    public function test_friendly_message_when_api_key_is_missing(): void
    {
        Http::fake();
        $settings = ChatbotSetting::first();
        $settings->update(['enabled' => true, 'api_key' => null]);
        config(['services.openai.key' => null]);

        $response = $this->postJson('/api/chat/message', ['message' => 'Hello?'])->assertOk();

        $this->assertStringContainsString('not available', strtolower($response->json('reply')));
        $this->assertTrue($response->json('error'));
        Http::assertNothingSent();
    }

    public function test_openai_failure_is_handled_gracefully(): void
    {
        Http::fake(['api.openai.com/*' => Http::response(['error' => ['message' => 'server error']], 500)]);

        $response = $this->postJson('/api/chat/message', ['message' => 'Hello?'])->assertOk();

        $this->assertTrue($response->json('error'));
        $this->assertNotEmpty($response->json('reply'));
        // The visitor message is still recorded so nothing is silently lost.
        $conversation = ChatConversation::where('uuid', $response->json('conversation_token'))->first();
        $this->assertSame(2, $conversation->messages()->count());
    }

    public function test_message_validation_rejects_empty_input(): void
    {
        $this->postJson('/api/chat/message', ['message' => ''])->assertStatus(422);
        $this->postJson('/api/chat/message', [])->assertStatus(422);
    }

    public function test_chat_messages_are_rate_limited(): void
    {
        Http::fake(['api.openai.com/*' => Http::response($this->openAiReply('ok'))]);

        $statuses = [];
        for ($i = 0; $i <= 12; $i++) {
            $statuses[] = $this->postJson('/api/chat/message', ['message' => 'message ' . $i])->status();
        }

        $this->assertContains(429, $statuses, 'The 13th request within a minute should be throttled');
    }

    public function test_admin_chatbot_endpoints_require_authentication(): void
    {
        $this->getJson('/api/admin/chatbot/settings')->assertStatus(401);
        $this->getJson('/api/admin/chatbot/conversations')->assertStatus(401);
        $this->getJson('/api/admin/chatbot/leads')->assertStatus(401);
    }

    public function test_admin_can_load_and_save_settings_and_the_key_stays_hidden(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/admin/chatbot/settings')
            ->assertOk()
            ->assertJson(['has_api_key' => false]);

        $this->postJson('/api/admin/chatbot/settings', [
            'model' => 'gpt-4o',
            'enabled' => true,
            'api_key' => 'sk-live-test-1234567890',
            'suggested_questions' => ['A', '', 'B'],
            'services' => [
                ['name' => 'Umrah Packages', 'description' => 'Luxury Umrah'],
                ['name' => '', 'description' => 'should be dropped'],
            ],
        ])->assertOk();

        // Stored encrypted, decryptable server-side.
        $settings = ChatbotSetting::first();
        $this->assertSame('sk-live-test-1234567890', $settings->api_key);
        $this->assertSame('gpt-4o', $settings->model);
        $this->assertSame(['A', 'B'], $settings->suggested_questions);
        $this->assertCount(1, $settings->services);

        $reload = $this->getJson('/api/admin/chatbot/settings')->assertOk();
        $this->assertTrue($reload->json('has_api_key'));
        $this->assertArrayNotHasKey('api_key', $reload->json());
        $this->assertStringNotContainsString('sk-live-test-1234567890', $reload->getContent());
    }

    public function test_admin_can_view_conversations_leads_and_update_lead_status(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $conversation = ChatConversation::create(['status' => 'open', 'last_activity_at' => now()]);
        ChatMessage::create(['conversation_id' => $conversation->id, 'role' => 'user', 'content' => 'Hi']);
        $lead = ChatLead::create([
            'conversation_id' => $conversation->id,
            'name' => 'Sara',
            'phone' => '12345',
            'service_interest' => 'Visa',
            'consent' => true,
            'status' => 'new',
        ]);

        $this->getJson('/api/admin/chatbot/conversations')->assertOk()->assertJsonStructure(['data']);
        $this->getJson('/api/admin/chatbot/conversations/' . $conversation->id)->assertOk();

        $this->getJson('/api/admin/chatbot/leads')->assertOk()->assertJsonPath('data.0.name', 'Sara');

        $this->patchJson('/api/admin/chatbot/leads/' . $lead->id, [
            'status' => 'contacted',
            'notes' => 'Called, awaiting reply',
        ])->assertOk();

        $this->assertDatabaseHas('chat_leads', ['id' => $lead->id, 'status' => 'contacted', 'notes' => 'Called, awaiting reply']);

        $this->deleteJson('/api/admin/chatbot/leads/' . $lead->id)->assertOk();
        $this->assertDatabaseMissing('chat_leads', ['id' => $lead->id]);

        $this->deleteJson('/api/admin/chatbot/conversations/' . $conversation->id)->assertOk();
        $this->assertDatabaseMissing('chat_conversations', ['id' => $conversation->id]);
    }
}
