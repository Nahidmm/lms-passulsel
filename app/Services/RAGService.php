<?php

namespace App\Services;

use App\Models\DokumenAi;
use App\Models\DokumenChunk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class RAGService
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Process a PDF document: extract text, chunk it, and save embeddings
     */
    public function processPdf(DokumenAi $doc): bool
    {
        try {
            $filePath = Storage::disk('public')->path($doc->file_path);
            
            if (!file_exists($filePath)) {
                Log::error("PDF file not found for processing: {$filePath}");
                return false;
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $pages = $pdf->getPages();

            // Delete existing chunks if re-processing
            $doc->chunks()->delete();

            $chunkText = '';
            $maxWordsPerChunk = 300; // rough chunking strategy

            foreach ($pages as $pageNumber => $page) {
                $text = $page->getText();
                // clean text
                $text = preg_replace('/\s+/', ' ', trim($text));

                if (empty($text)) {
                    continue;
                }

                $words = explode(' ', $text);
                
                foreach ($words as $word) {
                    $chunkText .= $word . ' ';
                    
                    if (str_word_count($chunkText) >= $maxWordsPerChunk) {
                        $this->saveChunk($doc, trim($chunkText));
                        $chunkText = '';
                    }
                }
            }

            // Save remaining text
            if (!empty(trim($chunkText))) {
                $this->saveChunk($doc, trim($chunkText));
            }

            return true;
        } catch (\Exception $e) {
            Log::error("RAG processing failed for doc ID {$doc->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save a single chunk to the database with its embedding
     */
    protected function saveChunk(DokumenAi $doc, string $text)
    {
        // Don't save very small useless chunks
        if (strlen($text) < 20) return;

        $embedding = $this->geminiService->getEmbedding($text);

        if ($embedding) {
            DokumenChunk::create([
                'dokumen_ai_id' => $doc->id,
                'chunk_text' => $text,
                'embedding' => $embedding
            ]);
        }
    }

    /**
     * Search for relevant chunks based on a query
     * Returns an array of associative arrays with 'chunk' and 'similarity'
     */
    public function searchRelevantChunks(string $query, int $limit = 3): array
    {
        $queryEmbedding = $this->geminiService->getEmbedding($query);
        
        if (!$queryEmbedding) {
            return [];
        }

        $allChunks = DokumenChunk::with('dokumenAi')->get();
        $results = [];

        foreach ($allChunks as $chunk) {
            $similarity = $this->cosineSimilarity($queryEmbedding, $chunk->embedding);
            $results[] = [
                'chunk' => $chunk,
                'similarity' => $similarity
            ];
        }

        // Sort by similarity descending
        usort($results, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Return top N
        return array_slice($results, 0, $limit);
    }

    /**
     * Calculate cosine similarity between two vectors
     */
    protected function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($vecA); $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += pow($vecA[$i], 2);
            $normB += pow($vecB[$i], 2);
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
