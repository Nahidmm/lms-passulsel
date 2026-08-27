@extends('layouts.app')
@section('title', 'Mission: ' . $materi->judul)

@section('content')
<div class="space-y-5 py-1">

    {{-- Breadcrumb --}}
    <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pelatihan
    </a>

    {{-- Hero --}}
    <div class="game-hero p-6 md:p-8">
        <div class="game-hero-orb-1"></div>
        <div class="game-hero-orb-2"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-end gap-6">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="badge badge-violet">
                        <i data-lucide="sword" class="w-3 h-3"></i>
                        Misi {{ $materi->urutan }}
                    </span>
                    @if($progres->status === 'selesai')
                        <span class="badge badge-emerald">
                            <i data-lucide="check-circle" class="w-3 h-3"></i> Selesai
                        </span>
                    @endif
                </div>
                <h1 class="font-black text-2xl md:text-4xl text-[var(--text-primary)] leading-tight">{{ $materi->judul }}</h1>
                <p class="text-[var(--text-primary)] mt-3 max-w-2xl leading-relaxed text-sm md:text-base">
                    {{ $materi->deskripsi ?: 'Pelajari materi ini, serap ilmunya, dan selesaikan tantangan yang ada.' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3 shrink-0">
                <div class="flex items-center gap-2 bg-black/30 border border-[var(--border)] rounded-xl px-4 py-2.5">
                    <i data-lucide="clock" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                    <div>
                        <p class="text-[9px] text-[var(--text-muted)] uppercase tracking-widest font-bold">Durasi</p>
                        <p class="text-sm font-black text-[var(--text-primary)]">{{ $materi->durasi_baca }} mnt</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-amber-500/15 border border-amber-500/25 rounded-xl px-4 py-2.5">
                    <i data-lucide="star" class="w-4 h-4 text-amber-400"></i>
                    <div>
                        <p class="text-[9px] text-amber-400/70 uppercase tracking-widest font-bold">Reward</p>
                        <p class="text-sm font-black text-amber-400">+{{ $materi->poin ?? 50 }} XP</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info chips --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="stat-card stat-card-violet">
            <p class="text-[9px] text-violet-400 uppercase tracking-widest font-bold mb-1">Tipe</p>
            <p class="text-base font-black text-[var(--text-primary)]">{{ $materi->jenis === 'quiz' ? 'Boss Battle' : ucfirst($materi->jenis) }}</p>
        </div>
        <div class="stat-card stat-card-cyan">
            <p class="text-[9px] text-cyan-400 uppercase tracking-widest font-bold mb-1">Status</p>
            <p class="text-base font-black {{ $progres->status === 'selesai' ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ $progres->status === 'selesai' ? 'Selesai âœ“' : 'Belum Selesai' }}
            </p>
        </div>
        <div class="stat-card stat-card-rose">
            <p class="text-[9px] text-rose-400 uppercase tracking-widest font-bold mb-1">Nilai Min.</p>
            <p class="text-base font-black text-[var(--text-primary)]">{{ $materi->passing_grade ?? 70 }}%</p>
        </div>
        <div class="stat-card stat-card-amber">
            <p class="text-[9px] text-amber-400 uppercase tracking-widest font-bold mb-1">XP Reward</p>
            <p class="text-base font-black text-amber-400">{{ $materi->poin ?? 50 }} XP</p>
        </div>
    </div>

    {{-- Content area --}}
    <div class="game-card p-5 md:p-8 min-h-80">
        @if($materi->jenis === 'link')
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-18 h-18 rounded-2xl bg-violet-500/15 border border-violet-500/25 flex items-center justify-center mb-6 mx-auto">
                    <i data-lucide="external-link" class="w-9 h-9 text-violet-400"></i>
                </div>
                <h3 class="font-black text-xl text-[var(--text-primary)] mb-2">Tautan Eksternal</h3>
                <p class="text-[var(--text-secondary)] text-sm mb-7 max-w-sm leading-relaxed">Materi ini membutuhkan kamu membuka tautan eksternal untuk mempelajarinya.</p>
                <a href="{{ $materi->url_link }}" target="_blank" class="btn btn-primary">
                    <i data-lucide="external-link" class="w-4 h-4"></i> Buka Tautan
                </a>
            </div>

        @elseif($materi->jenis === 'video_embed')
            @php $embedUrl = str_replace('watch?v=', 'embed/', $materi->url_link); @endphp
            <div class="aspect-video w-full rounded-xl overflow-hidden border border-violet-500/25 bg-black">
                <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>

        @elseif($materi->jenis === 'quiz')
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-20 h-20 rounded-full bg-rose-500/15 border-4 border-rose-500/50 flex items-center justify-center mb-6 mx-auto shadow-glow-rose">
                    <i data-lucide="skull" class="w-10 h-10 text-rose-400"></i>
                </div>
                <h3 class="font-black text-2xl text-[var(--text-primary)] mb-3">Boss Battle!</h3>
                <p class="text-[var(--text-primary)] text-base mb-8 max-w-sm leading-relaxed">
                    Kamu harus meraih nilai minimal <strong class="text-rose-400">{{ $materi->passing_grade ?? 70 }}</strong> untuk melewati tantangan ini.
                </p>
                <form action="{{ route('peserta.evaluasi.start') }}" method="POST">
                    @csrf
                    <input type="hidden" name="materi_id" value="{{ $materi->id }}">
                    <button type="submit" class="btn btn-danger text-base px-10 py-3.5">
                        <i data-lucide="swords" class="w-5 h-5"></i> Mulai Pertempuran
                    </button>
                </form>
            </div>

        @else
            @if($materi->file_path)
                <div class="w-full h-[650px] rounded-xl overflow-hidden border border-[var(--border)] bg-[var(--card)] border border-[var(--border)] shadow-sm">
                    <iframe src="{{ Storage::url($materi->file_path) }}" class="w-full h-full border-0"></iframe>
                </div>
            @else
                <div class="flex flex-col items-center py-20 text-center">
                    <i data-lucide="file-question" class="w-12 h-12 text-slate-700 mb-4"></i>
                    <p class="text-[var(--text-muted)] font-semibold">File materi tidak ditemukan.</p>
                </div>
            @endif
        @endif
    </div>

    {{-- Footer CTA --}}
    <div class="game-card p-4 md:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-start gap-2 text-sm text-[var(--text-secondary)]">
            <i data-lucide="{{ $materi->jenis === 'quiz' ? 'target' : 'info' }}" class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5"></i>
            @if($materi->jenis === 'quiz')
                <span><strong class="text-[var(--text-primary)]">Tujuan:</strong> Kalahkan boss untuk menyelesaikan tahap ini.</span>
            @else
                <span><strong class="text-[var(--text-primary)]">Tujuan:</strong> Pelajari materi, lalu tandai sebagai selesai.</span>
            @endif
        </div>

        @if($progres->status !== 'selesai')
            <form action="{{ route('peserta.pembelajaran.materi.progress', $materi->id) }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                    Tandai Selesai
                </button>
            </form>
        @else
            <div class="flex items-center gap-2 text-emerald-400 font-bold text-sm bg-emerald-500/10 border border-emerald-500/25 px-5 py-2.5 rounded-xl">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Sudah Selesai
            </div>
        @endif
    </div>

</div>
@endsection

