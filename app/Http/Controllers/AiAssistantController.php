<?php

namespace App\Http\Controllers;

use App\Models\AiChatHistory;
use App\Models\DokumenAi;
use App\Services\GeminiService;
use App\Services\RAGService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    private GeminiService $gemini;
    private RAGService $rag;

    public function __construct(GeminiService $gemini, RAGService $rag)
    {
        $this->gemini = $gemini;
        $this->rag = $rag;
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

        // Get recent history for context
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

        // Retrieve relevant knowledge from PDFs using RAG
        $ragContext = '';
        $referenceDocs = [];
        $relevantChunks = collect($this->rag->searchRelevantChunks($message, 3)); // top 3 chunks
        
        if ($relevantChunks->isNotEmpty()) {
            // we only want chunks with a similarity score > 0.5 roughly, but for now we'll take top 3
            // to avoid injecting irrelevant data.
            $goodChunks = $relevantChunks->filter(function($item) {
                return $item['similarity'] > 0.3; // threshold
            });

            foreach ($goodChunks as $item) {
                $chunk = $item['chunk'];
                $ragContext .= "Dari Dokumen '{$chunk->dokumenAi->judul}':\n" . $chunk->chunk_text . "\n\n";
                $referenceDocs[$chunk->dokumen_ai_id] = [
                    'id' => $chunk->dokumen_ai_id,
                    'judul' => $chunk->dokumenAi->judul,
                    'file_path' => $chunk->dokumenAi->file_path,
                ];
            }
        }

        try {
            // Call Gemini
            $response = $this->gemini->chat($message, $history, $ragContext);

            // Save AI Response with references
            $aiMessage = AiChatHistory::create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $response,
                'session_id' => $sessionId,
                'references' => empty($referenceDocs) ? null : array_values($referenceDocs),
            ]);

            return response()->json([
                'success' => true,
                'message' => $response,
                'data' => $aiMessage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
