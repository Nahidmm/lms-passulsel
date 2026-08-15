@extends('layouts.app')

@section('title', $pelatihan->judul)

@section('content')
<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('peserta.pelatihan.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors bg-white px-3 py-1.5 rounded-lg border border-border shadow-sm">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

<!-- Header Pelatihan -->
<div class="bg-white rounded-2xl shadow-sm border border-border p-6 md:p-8 mb-6 flex flex-col md:flex-row gap-8">
    <div class="flex-1">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-primary/10 text-primary text-xs font-bold mb-4 w-max">
            Terbuka
        </div>
        <h1 class="text-3xl font-display font-bold mb-3 text-text-primary">{{ $pelatihan->judul }}</h1>
        <p class="text-text-secondary text-base leading-relaxed mb-6">{{ $pelatihan->deskripsi }}</p>
        
        <div class="flex items-center gap-6 mt-auto">
            <div class="flex items-center gap-2 text-sm text-text-secondary">
                <div class="w-8 h-8 rounded bg-secondary flex items-center justify-center text-primary">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                </div>
                <span class="font-bold text-text-primary">{{ $materis->count() }}</span> Materi
            </div>
            <div class="flex items-center gap-2 text-sm text-text-secondary">
                <div class="w-8 h-8 rounded bg-secondary flex items-center justify-center text-primary">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <span class="font-bold text-text-primary">{{ $materis->sum('durasi_baca') + $materis->sum('durasi_menit') }}</span> Menit
            </div>
        </div>
    </div>
    <div class="w-full md:w-1/3 flex flex-col justify-center items-center bg-secondary/30 rounded-xl p-6 border border-border">
        <div class="text-center w-full">
            <p class="text-sm font-bold text-text-secondary mb-2 uppercase tracking-wide">Progres Belajar</p>
            <div class="text-4xl font-display font-bold text-primary mb-4">{{ $persenProgress }}%</div>
            <div class="w-full bg-secondary rounded-full h-3 overflow-hidden">
                <div class="bg-primary h-3 rounded-full transition-all duration-1000" style="width: {{ $persenProgress }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- List Materi -->
<div class="bg-white rounded-2xl shadow-sm border border-border p-6 md:p-8 mb-6">
    <div class="mb-6 border-b border-border pb-4">
        <h2 class="text-xl font-bold text-text-primary">Kurikulum Pembelajaran</h2>
    </div>

    @if($materis->isEmpty())
        <div class="text-center py-12 bg-secondary/30 rounded-xl border border-dashed border-border">
            <i data-lucide="inbox" class="w-8 h-8 text-text-secondary mx-auto mb-3"></i>
            <p class="text-text-secondary font-medium">Materi belum tersedia di pelatihan ini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($materis as $idx => $materi)
                @php
                    $statusData = $materiStatus[$materi->id] ?? null;
                    $isLocked = $statusData ? $statusData['is_locked'] : false;
                    $statusProgres = $statusData ? $statusData['status'] : 'belum';
                @endphp
                
                @if($isLocked)
                <div class="block p-4 rounded-xl border border-border bg-gray-50 opacity-60 cursor-not-allowed">
                @else
                <a href="{{ route('peserta.pembelajaran.materi.show', $materi->id) }}" class="block p-4 rounded-xl border border-border hover:border-primary/50 hover:shadow-sm bg-white transition-all group">
                @endif
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg transition-colors
                            @if($statusProgres === 'selesai') bg-success/10 text-success
                            @elseif($isLocked) bg-border text-text-secondary
                            @else bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white @endif">
                            @if($statusProgres === 'selesai') <i data-lucide="check" class="w-6 h-6"></i>
                            @elseif($isLocked) <i data-lucide="lock" class="w-5 h-5"></i>
                            @else {{ $idx + 1 }} @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-base {{ $isLocked ? 'text-text-secondary' : 'text-text-primary group-hover:text-primary' }} transition-colors">{{ $materi->judul }}</h4>
                            <div class="flex items-center gap-3 mt-1.5 text-xs font-medium text-text-secondary">
                                <span class="flex items-center gap-1.5 uppercase tracking-wider">
                                    @if($materi->jenis === 'video_embed') <i data-lucide="video" class="w-3.5 h-3.5 {{ $isLocked ? '' : 'text-primary' }}"></i> Video
                                    @elseif($materi->jenis === 'link') <i data-lucide="link" class="w-3.5 h-3.5 {{ $isLocked ? '' : 'text-primary' }}"></i> Link
                                    @elseif($materi->jenis === 'quiz') <i data-lucide="help-circle" class="w-3.5 h-3.5 {{ $isLocked ? '' : 'text-accent' }}"></i> Kuis
                                    @else <i data-lucide="file-text" class="w-3.5 h-3.5 {{ $isLocked ? '' : 'text-primary' }}"></i> {{ $materi->jenis }}
                                    @endif
                                </span>
                                <span>&bull;</span>
                                <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ $materi->jenis === 'quiz' ? ($materi->durasi_menit ?: 'Tanpa batas') : $materi->durasi_baca }} mnt</span>
                                
                                @if($materi->prasyarat_materi_id)
                                <span>&bull;</span>
                                <span class="text-accent/90 flex items-center gap-1.5 bg-accent/10 px-2 py-0.5 rounded"><i data-lucide="key" class="w-3 h-3"></i> Bersyarat</span>
                                @endif
                            </div>
                        </div>
                        @if(!$isLocked)
                            <div class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center text-text-secondary group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </div>
                        @endif
                    </div>
                @if($isLocked)
                </div>
                @else
                </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
