@extends('layouts.app')

@section('title', 'Materi: ' . $materi->judul)

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('peserta.pembelajaran.show', $materi->modul_id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Modul
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8 mb-6">
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8 pb-6 border-b border-border">
        <div>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3">Materi {{ $materi->urutan }}</span>
            <h1 class="text-2xl md:text-3xl font-display font-bold text-text-primary">{{ $materi->judul }}</h1>
            <p class="text-text-secondary mt-3 leading-relaxed">{{ $materi->deskripsi }}</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3 bg-secondary px-4 py-3 rounded-lg border border-border">
            <i data-lucide="clock" class="text-text-secondary w-5 h-5"></i>
            <div>
                <p class="text-xs font-medium text-text-secondary">Estimasi Waktu</p>
                <p class="font-bold text-text-primary">{{ $materi->durasi_baca }} Menit</p>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="min-h-[400px] mb-8">
        @if($materi->jenis === 'link')
            <div class="text-center py-16 bg-secondary/50 rounded-xl border border-dashed border-border">
                <i data-lucide="external-link" class="w-12 h-12 text-primary mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-text-primary mb-2">Tautan Eksternal</h3>
                <p class="text-text-secondary mb-6">Materi ini mengarah ke situs web eksternal.</p>
                <a href="{{ $materi->url_link }}" target="_blank" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-6 rounded-lg transition-colors">
                    Buka Tautan <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                </a>
            </div>
        @elseif($materi->jenis === 'video_embed')
            <div class="aspect-video w-full rounded-xl overflow-hidden shadow-sm border border-border">
                @php
                    $url = $materi->url_link;
                    $embedUrl = str_replace('watch?v=', 'embed/', $url);
                @endphp
                <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        @elseif($materi->jenis === 'quiz')
            <div class="text-center py-16 bg-secondary/50 rounded-xl border border-dashed border-border">
                <i data-lucide="help-circle" class="w-12 h-12 text-accent mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-text-primary mb-2">Evaluasi / Kuis</h3>
                <p class="text-text-secondary mb-6">Kerjakan kuis ini untuk menguji pemahaman Anda.</p>
                <!-- For now, we don't have a direct link to the quiz execution for a specific materi since evaluasi might be global. But let's assume there is a route for it. -->
                <a href="#" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-hover text-white font-bold py-2.5 px-6 rounded-lg transition-colors">
                    Mulai Kuis <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        @else
            <!-- PDF / PPTX Viewer -->
            @if($materi->file_path)
                <div class="w-full h-[600px] rounded-xl overflow-hidden border border-border bg-secondary">
                    <iframe src="{{ Storage::url($materi->file_path) }}" class="w-full h-full border-0"></iframe>
                </div>
            @else
                <div class="text-center py-16 text-text-secondary bg-secondary/30 rounded-xl border border-dashed border-border">File materi tidak ditemukan.</div>
            @endif
        @endif
    </div>

    <!-- Action Bar -->
    <div class="flex justify-end pt-6 border-t border-border">
        @if($progres->status !== 'selesai')
            <form action="{{ route('peserta.pembelajaran.materi.progress', $materi->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-success hover:bg-success/90 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors text-lg">
                    <i data-lucide="check-circle" class="w-5 h-5"></i> Tandai Selesai
                </button>
            </form>
        @else
            <div class="inline-flex items-center gap-2 bg-success/10 border border-success text-success font-bold py-3 px-8 rounded-lg">
                <i data-lucide="check-circle" class="w-5 h-5"></i> Materi Selesai
            </div>
        @endif
    </div>
</div>

@endsection
