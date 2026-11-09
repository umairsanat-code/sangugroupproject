<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatLead;
use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

/**
 * Admin API for the AI Chatbot module. All routes live behind auth:sanctum.
 * The raw OpenAI API key is never returned (the model hides it).
 */
class ChatbotAdminController extends Controller
{
    public function __construct(protected ChatbotService $chatbot)
    {
    }

    public function getSettings()
    {
        return response()->json($this->chatbot->settings());
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'sometimes|boolean',
            'model' => 'required|string|max:100',
            'system_prompt' => 'nullable|string|max:8000',
            'welcome_message' => 'nullable|string|max:1000',
            'suggested_questions' => 'nullable|array|max:10',
            'suggested_questions.*' => 'nullable|string|max:300',
            'services' => 'nullable|array|max:30',
            'services.*.name' => 'nullable|string|max:200',
            'services.*.description' => 'nullable|string|max:500',
            'faqs' => 'nullable|array|max:50',
            'faqs.*.question' => 'nullable|string|max:300',
            'faqs.*.answer' => 'nullable|string|max:2000',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:16|max:4096',
            'lead_capture_enabled' => 'sometimes|boolean',
            'api_key' => 'nullable|string|max:400',
            'clear_api_key' => 'sometimes|boolean',
        ]);

        $settings = $this->chatbot->settings();

        $fields = [
            'enabled', 'model', 'system_prompt', 'welcome_message',
            'suggested_questions', 'services', 'faqs', 'temperature',
            'max_tokens', 'lead_capture_enabled',
        ];

        $update = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $validated)) {
                $update[$field] = $validated[$field];
            }
        }

        // Normalise repeatable groups: drop empty rows.
        if (array_key_exists('suggested_questions', $update)) {
            $update['suggested_questions'] = array_values(array_filter(
                array_map(fn ($q) => trim((string) $q), $update['suggested_questions'] ?? []),
                fn ($q) => $q !== ''
            ));
        }
        if (array_key_exists('services', $update)) {
            $update['services'] = array_values(array_filter(
                $update['services'] ?? [],
                fn ($s) => trim((string) ($s['name'] ?? '')) !== ''
            ));
        }
        if (array_key_exists('faqs', $update)) {
            $update['faqs'] = array_values(array_filter(
                $update['faqs'] ?? [],
                fn ($f) => trim((string) ($f['question'] ?? '')) !== '' || trim((string) ($f['answer'] ?? '')) !== ''
            ));
        }

        // API key handling: only overwrite when a new value is supplied;
        // "clear_api_key" removes it and falls back to the environment variable.
        if (! empty($validated['clear_api_key'])) {
            $update['api_key'] = null;
        } elseif (array_key_exists('api_key', $validated) && trim((string) $validated['api_key']) !== '') {
            $update['api_key'] = trim($validated['api_key']);
        }

        $settings->update($update);

        return response()->json([
            'message' => 'Chatbot settings saved successfully',
            'settings' => $settings->fresh(),
        ]);
    }

    public function stats()
    {
        return response()->json([
            'conversations' => ChatConversation::count(),
            'messages' => ChatMessage::count(),
            'leads' => ChatLead::count(),
            'new_leads' => ChatLead::where('status', 'new')->count(),
        ]);
    }

    public function conversations(Request $request)
    {
        $query = ChatConversation::query()
            ->withCount('messages')
            ->with('lead')
            ->orderByDesc('last_activity_at')
            ->orderByDesc('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = trim((string) $request->input('search'))) {
            $query->whereHas('messages', fn ($q) => $q->where('content', 'like', '%' . $search . '%'));
        }

        return $query->paginate((int) $request->input('per_page', 20));
    }

    public function showConversation($id)
    {
        $conversation = ChatConversation::with([
            'messages' => fn ($q) => $q->orderBy('id'),
            'leads',
        ])->findOrFail($id);

        return response()->json($conversation);
    }

    public function destroyConversation($id)
    {
        $conversation = ChatConversation::findOrFail($id);
        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }

    public function leads(Request $request)
    {
        $query = ChatLead::query()
            ->with(['conversation:id,uuid,page_url'])
            ->orderByDesc('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('service_interest', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate((int) $request->input('per_page', 20));
    }

    public function updateLead(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', ChatLead::STATUSES),
            'notes' => 'nullable|string|max:5000',
        ]);

        $lead = ChatLead::findOrFail($id);
        $lead->update($validated);

        return response()->json(['message' => 'Lead updated', 'lead' => $lead]);
    }

    public function destroyLead($id)
    {
        ChatLead::findOrFail($id)->delete();

        return response()->json(['message' => 'Lead deleted']);
    }
}
