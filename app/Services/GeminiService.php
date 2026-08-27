<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;
    private string $embeddingModel;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->model = 'gemini-3.6-flash';
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
        $this->embeddingModel = 'gemini-embedding-2';
    }

    public function chat(string $prompt, array $history = [], string $ragContext = ''): ?string
    {
        if (empty($this->apiKey) || $this->apiKey === 'your-gemini-api-key-here') {
            Log::error('Gemini API key is not configured.');
            return "Maaf, sistem AI sedang tidak tersedia (API Key belum dikonfigurasi).";
        }

        $contents = [];
        
        // System instructions (prepended to history context if any)
        $systemContext = "Anda adalah AI Assistant resmi untuk LMS Pemasyarakatan Sulawesi Selatan. Tugas Anda adalah membantu pegawai pemasyarakatan. Anda memiliki akses ke Dokumen Referensi internal (Knowledge Base).";

        if (!empty($ragContext)) {
            $systemContext .= "\n\nPENTING! Anda WAJIB menjawab pertanyaan HANYA berdasarkan informasi yang ada di dalam Dokumen Referensi berikut. Jangan pernah mengarang jawaban atau menggunakan pengetahuan dari luar dokumen ini. Jika jawabannya tidak ada di dalam dokumen referensi ini, katakan: 'Maaf, informasi tersebut tidak ditemukan dalam dokumen referensi saya.'\n\n--- DOKUMEN REFERENSI ---\n" . $ragContext . "\n--- AKHIR REFERENSI ---\n";
        } else {
             $systemContext .= "\n\nSaat ini belum ada dokumen referensi yang cocok dengan pertanyaan pengguna. Berikan jawaban umum yang sesuai jika memungkinkan, namun ingatkan pengguna bahwa ini bukan dari referensi resmi.";
        }

        if (empty($history)) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => "Konteks sistem: $systemContext\n\nPertanyaan pengguna: $prompt"]]
            ];
        } else {
            // Append history
            foreach ($history as $msg) {
                // Gemini roles: 'user' or 'model'
                $role = $msg['role'] === 'assistant' ? 'model' : 'user';
                $text = $msg['content'];
                
                // If it's the first message, prepend system context
                if ($msg === $history[0] && $role === 'user') {
                    $text = "Konteks sistem: $systemContext\n\nPertanyaan pengguna: " . $text;
                }

                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]]
                ];
            }
            // Add current prompt
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt]]
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.4,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Gemini Success Response: ' . json_encode($data));
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
            }

            Log::error('Gemini API Error: ' . $response->body());
            throw new \Exception("Maaf, terjadi kesalahan saat menghubungi server AI. Coba lagi nanti.");
            
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            throw new \Exception("Maaf, layanan AI sedang mengalami gangguan koneksi.");
        }
    }

    /**
     * Get vector embedding for a given text
     */
    public function getEmbedding(string $text): ?array
    {
        if (empty($this->apiKey) || $this->apiKey === 'your-gemini-api-key-here') {
            Log::error('Gemini API key is not configured for embeddings.');
            return null;
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->embeddingModel}:embedContent";
        
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url . '?key=' . $this->apiKey, [
                'model' => "models/{$this->embeddingModel}",
                'content' => [
                    'parts' => [
                        ['text' => $text]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['embedding']['values'])) {
                    return $data['embedding']['values'];
                }
            }

            Log::error('Gemini Embedding API Error: ' . $response->body());
            return null;
            
        } catch (\Exception $e) {
            Log::error('Gemini Embedding Exception: ' . $e->getMessage());
            return null;
        }
    }
}
