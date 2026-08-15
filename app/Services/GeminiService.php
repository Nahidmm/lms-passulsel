<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->model = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    public function chat(string $prompt, array $history = []): ?string
    {
        if (empty($this->apiKey) || $this->apiKey === 'your-gemini-api-key-here') {
            Log::error('Gemini API key is not configured.');
            return "Maaf, sistem AI sedang tidak tersedia (API Key belum dikonfigurasi).";
        }

        $contents = [];
        
        // System instructions (prepended to history context if any)
        $systemContext = "Anda adalah AI Assistant resmi untuk LMS Pemasyarakatan Sulawesi Selatan. Tugas Anda adalah membantu para pejabat eselon V (seperti Kepala Seksi di Lapas/Rutan) memahami tugas pokok dan fungsi (tupoksi), regulasi Kemenkumham, serta memberikan panduan dan contoh laporan. Jawab dengan bahasa Indonesia yang formal, sopan, namun mudah dipahami. Jangan menjawab pertanyaan yang tidak relevan dengan pemasyarakatan atau tugas pegawai pemerintah.";

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
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "Maaf, terjadi kesalahan saat menghubungi server AI. Coba lagi nanti.";
            
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return "Maaf, layanan AI sedang mengalami gangguan koneksi.";
        }
    }
}
