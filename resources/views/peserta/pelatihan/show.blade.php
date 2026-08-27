@extends('layouts.app')
@section('title', $pelatihan->judul)

@section('content')
<div class="space-y-5 py-1">

    {{-- Back link --}}
    <a href="{{ route('peserta.pelatihan.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Katalog
    </a>

    {{-- Hero --}}
    <div class="game-hero p-6 md:p-8">
        <div class="game-hero-orb-1"></div>
        <div class="game-hero-orb-2"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-end gap-6">
            <div class="flex-1">
                <span class="badge badge-violet mb-3">
                    <i data-lucide="swords" class="w-3 h-3"></i> Mission Board
                </span>
                <h1 class="text-2xl md:text-4xl font-black text-[var(--text-primary)] leading-tight mt-2">{{ $pelatihan->judul }}</h1>
                <p class="text-[var(--text-primary)] mt-3 max-w-2xl leading-relaxed text-sm md:text-base">{{ $pelatihan->deskripsi }}</p>
            </div>

            <div class="flex flex-wrap gap-3 shrink-0">
                <div class="flex items-center gap-2 bg-black/30 border border-[var(--border)] rounded-xl px-4 py-2.5">
                    <i data-lucide="layers" class="w-4 h-4 text-violet-400"></i>
                    <div>
                        <p class="text-[9px] text-[var(--text-muted)] uppercase tracking-widest font-bold">Materi</p>
                        <p class="text-sm font-black text-[var(--text-primary)]">{{ $materis->count() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-black/30 border border-[var(--border)] rounded-xl px-4 py-2.5">
                    <i data-lucide="trending-up" class="w-4 h-4 text-cyan-400"></i>
                    <div>
                        <p class="text-[9px] text-[var(--text-muted)] uppercase tracking-widest font-bold">Progress</p>
                        <p class="text-sm font-black text-cyan-300">{{ $persenProgress }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat row --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="stat-card stat-card-violet text-center">
            <p class="text-2xl font-black text-[var(--text-primary)]">{{ $materis->count() }}</p>
            <p class="text-xs text-[var(--text-secondary)] font-semibold mt-1">Total Materi</p>
        </div>
        <div class="stat-card stat-card-emerald text-center">
            <p class="text-2xl font-black text-emerald-400">{{ $persenProgress }}%</p>
            <div class="progress-track mt-2">
                <div class="progress-bar-violet" style="width:{{ $persenProgress }}%"></div>
            </div>
            <p class="text-xs text-[var(--text-secondary)] font-semibold mt-2">Progress</p>
        </div>
        <div class="stat-card stat-card-amber text-center">
            <p class="text-2xl font-black text-[var(--text-primary)]">{{ $materis->sum('durasi_baca') + $materis->sum('durasi_menit') }}</p>
            <p class="text-xs text-[var(--text-secondary)] font-semibold mt-1">Menit Total</p>
        </div>
    </div>

    {{-- â•â•â• CERTIFICATE BANNER (muncul jika pelatihan selesai) â•â•â• --}}
    @if($progresPelatihan && $progresPelatihan->status === 'selesai')
    <div class="relative overflow-hidden rounded-2xl border border-emerald-500/30 bg-gradient-to-r from-emerald-950/80 via-teal-950/60 to-cyan-950/80 p-6 md:p-8">
        {{-- Decorative orbs --}}
        <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full bg-cyan-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-5">
            {{-- Icon --}}
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-8 h-8 text-emerald-400"></i>
            </div>

            {{-- Text --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="badge badge-emerald text-xs">ðŸŽ‰ Selamat!</span>
                </div>
                <h2 class="text-xl font-black text-[var(--text-primary)]">Pelatihan Berhasil Diselesaikan</h2>
                <p class="text-sm text-[var(--text-primary)] mt-1">Kamu telah menyelesaikan seluruh materi <strong class="text-[var(--text-primary)]">{{ $pelatihan->judul }}</strong>.</p>
                @if($sertifikat)
                <p class="text-xs text-[var(--text-muted)] mt-2 font-mono">
                    Credential ID: <span class="text-emerald-400 font-semibold">{{ $sertifikat->credential_id }}</span>
                    &nbsp;Â·&nbsp; Diterbitkan {{ $sertifikat->issued_at->translatedFormat('d F Y') }}
                </p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                @if($sertifikat)
                    <a href="{{ route('peserta.sertifikat.download', $sertifikat->credential_id) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-[var(--text-primary)] font-bold text-sm transition-all shadow-lg shadow-emerald-500/30">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Unduh Sertifikat PDF
                    </a>
                    <a href="{{ route('sertifikat.verify', $sertifikat->credential_id) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-[var(--border)] hover:bg-[var(--card)] border border-[var(--border)] shadow-sm text-[var(--text-primary)] font-bold text-sm transition-all">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Verifikasi
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-700/50 text-[var(--text-secondary)] font-bold text-sm">
                        <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
                        Sertifikat sedang disiapkan...
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Curriculum --}}
    <div class="game-card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--border)]">
            <h2 class="font-bold text-[var(--text-primary)] text-base">Kurikulum Pembelajaran</h2>
            <div class="flex items-center gap-2">
                <div class="progress-track w-28">
                    <div class="progress-bar-rainbow" style="width:{{ $persenProgress }}%"></div>
                </div>
                <span class="text-xs font-bold text-[var(--text-secondary)]">{{ $persenProgress }}%</span>
            </div>
        </div>

        @if($materis->isEmpty())
            <div class="flex flex-col items-center py-14">
                <i data-lucide="inbox" class="w-10 h-10 text-slate-700 mb-3"></i>
                <p class="text-[var(--text-muted)] font-semibold">Materi belum tersedia.</p>
            </div>
        @else
            <div class="divide-y divide-white/[0.04]">
                @foreach($materis as $idx => $materi)
                    @php
                        $statusData  = $materiStatus[$materi->id] ?? null;
                        $isLocked    = $statusData ? $statusData['is_locked']  : false;
                        $statusMateri= $statusData ? $statusData['status']      : 'belum';
                    @endphp

                    @if($isLocked)
                    <div class="flex items-center gap-4 px-5 py-4 opacity-50 cursor-not-allowed">
                    @else
                    <a href="{{ route('peserta.pembelajaran.materi.show', $materi->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[var(--card)] border border-[var(--border)] shadow-sm transition-colors group">
                    @endif
                        {{-- Number / status circle --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm shrink-0 transition-all
                            @if($statusMateri === 'selesai') bg-emerald-500/20 border border-emerald-500/30 text-emerald-400
                            @elseif($isLocked)               bg-slate-800 border border-white/8 text-slate-600
                            @else                            bg-violet-500/15 border border-violet-500/20 text-violet-400 group-hover:bg-violet-500/25 @endif">
                            @if($statusMateri === 'selesai') <i data-lucide="check" class="w-5 h-5"></i>
                            @elseif($isLocked)               <i data-lucide="lock" class="w-4 h-4"></i>
                            @else {{ $idx + 1 }} @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm md:text-base leading-snug
                                @if($isLocked) text-[var(--text-muted)] @else text-[var(--text-primary)] group-hover:text-violet-300 transition-colors @endif">
                                {{ $materi->judul }}
                            </h4>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                {{-- Type badge --}}
                                <span class="flex items-center gap-1 text-xs font-semibold
                                    @if($materi->jenis === 'quiz') text-amber-400 @else text-[var(--text-muted)] @endif">
                                    @if($materi->jenis === 'video_embed') <i data-lucide="video" class="w-3 h-3"></i> Video
                                    @elseif($materi->jenis === 'link')    <i data-lucide="link"  class="w-3 h-3"></i> Link
                                    @elseif($materi->jenis === 'quiz')    <i data-lucide="zap"   class="w-3 h-3"></i> Boss Battle
                                    @else                                  <i data-lucide="file-text" class="w-3 h-3"></i> Dokumen @endif
                                </span>
                                <span class="text-slate-600">Â·</span>
                                <span class="flex items-center gap-1 text-xs text-[var(--text-muted)]">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    {{ $materi->jenis === 'quiz' ? ($materi->durasi_menit ?: 'âˆž') : $materi->durasi_baca }} mnt
                                </span>
                                
                                {{-- XP Info --}}
                                <span class="text-slate-600">Â·</span>
                                <span class="flex items-center gap-1 text-xs font-semibold text-emerald-400" title="Poin XP maksimal yang bisa didapatkan">
                                    <i data-lucide="star" class="w-3 h-3"></i>
                                    {{ $materi->poin ?? 0 }} XP
                                </span>

                                @if($materi->jenis === 'quiz' && $materi->mode_tampilan === 'interaktif')
                                    <span class="badge badge-emerald text-[9px] px-1.5 py-0.5 ml-1" title="Mode Interaktif memberikan bonus +20% XP dari skor">
                                        <i data-lucide="zap" class="w-2.5 h-2.5"></i> +20% XP Bonus
                                    </span>
                                @endif

                                @if($materi->prasyarat_materi_id)
                                    <span class="badge badge-amber text-[9px] px-1.5 py-0.5 ml-1"><i data-lucide="key" class="w-2.5 h-2.5"></i> Bersyarat</span>
                                @endif
                            </div>
                        </div>

                        @if(!$isLocked)
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600 group-hover:text-violet-400 group-hover:translate-x-1 transition-all shrink-0"></i>
                        @endif

                    @if($isLocked) </div> @else </a> @endif
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection


