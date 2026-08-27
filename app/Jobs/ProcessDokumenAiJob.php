<?php

namespace App\Jobs;

use App\Models\DokumenAi;
use App\Services\RAGService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessDokumenAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dokumen;
    public $timeout = 600; // 10 minutes timeout

    /**
     * Create a new job instance.
     */
    public function __construct(DokumenAi $dokumen)
    {
        $this->dokumen = $dokumen;
    }

    /**
     * Execute the job.
     */
    public function handle(RAGService $ragService): void
    {
        // Process PDF and generate embeddings
        $ragService->processPdf($this->dokumen);
    }
}
