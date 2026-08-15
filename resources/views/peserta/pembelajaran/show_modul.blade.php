@extends('layouts.app')

@section('title', 'Modul: ' . $modul->judul)

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('peserta.pembelajaran.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Modul
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8 mb-6">
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8 pb-6 border-b border-border">
        <div>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3">Modul {{ $modul->urutan }}</span>
            <h1 class="text-2xl md:text-3xl font-display font-bold text-text-primary">{{ $modul->judul }}</h1>
            <p class="text-text-secondary mt-3 leading-relaxed">{{ $modul->deskripsi }}</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3 bg-secondary px-4 py-3 rounded-lg border border-border">
            <i data-lucide="layers" class="text-text-secondary w-5 h-5"></i>
            <div>
                <p class="text-xs font-medium text-text-secondary">Jumlah Materi</p>
                <p class="font-bold text-text-primary">{{ $materis->count() }} Materi</p>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-bold text-text-primary mb-4 flex items-center gap-2">
            <i data-lucide="list" class="w-5 h-5 text-primary"></i> Daftar Materi
        </h3>
        
        @if($materis->isEmpty())
            <p class="text-center text-text-secondary py-8 bg-secondary/30 rounded-xl border border-dashed border-border">Materi belum tersedia di modul ini.</p>
        @else
            <div class="space-y-4">
                @foreach($materis as $idx => $materi)
                    <a href="{{ route('peserta.pembelajaran.materi.show', $materi->id) }}" class="block p-4 rounded-xl border border-border hover:border-primary/50 bg-white hover:bg-secondary/30 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center border border-border font-bold text-text-primary group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                {{ $idx + 1 }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-text-primary group-hover:text-primary transition-colors">{{ $materi->judul }}</h4>
                                <div class="flex items-center gap-3 mt-1 text-xs font-medium text-text-secondary">
                                    <span class="flex items-center gap-1 uppercase tracking-wider">
                                        @if($materi->jenis === 'video_embed') <i data-lucide="video" class="w-3 h-3 text-primary"></i> Video
                                        @elseif($materi->jenis === 'link') <i data-lucide="link" class="w-3 h-3 text-primary"></i> Link
                                        @elseif($materi->jenis === 'quiz') <i data-lucide="help-circle" class="w-3 h-3 text-accent"></i> Kuis
                                        @else <i data-lucide="file-text" class="w-3 h-3 text-primary"></i> {{ $materi->jenis }}
                                        @endif
                                    </span>
                                    <span>&bull;</span>
                                    <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> {{ $materi->durasi_baca }} mnt</span>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-text-secondary group-hover:text-primary transition-colors"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
