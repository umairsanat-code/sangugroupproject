<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Public, unauthenticated endpoints used by the floating chat widget.
 * No secrets are ever returned here.
 */
class ChatController extends Controller
{
    public function __construct(protected ChatbotService $chatbot)
    {
    }

    /**
     * Widget configuration (welcome message, suggested questions, enabled flag).
     */
    public function config()
    {
        return response()->json($this->chatbot->publicConfig());
    }

    /**
     * Receive a visitor message, produce an AI reply and persist the turn.
     */
    public function message(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:1|max:2000',
            'conversation_token' => 'nullable|string|max:64',
            'page_url' => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->chatbot->handleMessage(
                trim($validated['message']),
                $validated['conversation_token'] ?? null,
                $request,
            );

            return response()->json($result);
        } catch (\Throwable $e) {
            // Absolute last-resort guard: the widget must never see a crash.
            Log::error('Chat endpoint failure: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'error' => true,
                'reply' => 'Sorry, something went wrong on our side. Please try again or contact us at ' . ChatbotService::EMAIL . '.',
                'conversation_token' => null,
            ], 200);
        }
    }

    /**
     * Restore a conversation transcript (used when the widget is reopened).
     */
    public function history(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|max:64',
        ]);

        $conversation = ChatConversation::query()
            ->where('uuid', $validated['token'])
            ->with(['messages' => fn ($q) => $q->orderBy('id')])
            ->first();

        if (! $conversation) {
            return response()->json(['messages' => []]);
        }

        $messages = $conversation->messages
            ->filter(fn ($m) => in_array($m->role, ['user', 'assistant'], true))
            ->map(fn ($m) => [
                'role' => $m->role,
                'content' => $m->content,
                'created_at' => optional($m->created_at)->toIso8601String(),
            ])
            ->values();

        return response()->json(['messages' => $messages]);
    }
}
