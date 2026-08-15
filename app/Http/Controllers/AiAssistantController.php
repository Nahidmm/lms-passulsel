<?php

namespace App\Http\Controllers;

use App\Models\AiChatHistory;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    private GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get or create session ID for the day
        $sessionId = $request->session()->get('ai_session_id');
        if (!$sessionId) {
            $sessionId = uniqid('session_');
            $request->session()->put('ai_session_id', $sessionId);
        }

        $histories = AiChatHistory::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('peserta.ai-assistant.index', compact('histories'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $message = $request->input('message');
        
        // Check if user is currently doing an evaluation (safety check, already handled in middleware but good to double check)
        if ($user->hasActiveSesiEvaluasi()) {
            return response()->json([
                'success' => false,
                'message' => 'AI Assistant tidak tersedia selama sesi evaluasi berlangsung.'
            ], 403);
        }

        $sessionId = $request->session()->get('ai_session_id', uniqid('session_'));

        // Save User Message
        AiChatHistory::create([
            'user_id' => $user->id,
            'role' => 'user',
            'content' => $message,
            'session_id' => $sessionId,
        ]);

        // Get recent history for context (last 10 messages)
        $history = AiChatHistory::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->map(function ($item) {
                return ['role' => $item->role, 'content' => $item->content];
            })
            ->values()
            ->toArray();

        // Call Gemini
        $response = $this->gemini->chat($message, $history);

        // Save AI Response
        $aiMessage = AiChatHistory::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => $response,
            'session_id' => $sessionId,
        ]);

        return response()->json([
            'success' => true,
            'message' => $response,
            'data' => $aiMessage
        ]);
    }
}
