@extends('layouts.app')

@section('title', 'Daftar Modul Pembelajaran')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Modul Pembelajaran</h1>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Pelajari seluruh materi kurikulum disiplin ASN secara bertahap dan terstruktur.</p>
        </div>
    </div>

    <!-- Modul Timeline List -->
    <div class="space-y-4">
        @forelse($moduls as $index => $modul)
            @php
                $status   = $modulStatus[$modul->id]['status'] ?? 'belum_mulai';
                $isLocked = $modulStatus[$modul->id]['is_locked'] ?? false;
                $total    = $modul->materis()->where('is_active', true)->count();
            @endphp

            <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-5 md:p-6 shadow-xs transition-all hover:border-primary/40 {{ $isLocked ? 'opacity-65' : '' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    
                    <div class="flex items-start gap-4">
                        <!-- Number Badge -->
                        <div class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center font-bold text-sm border
                            @if($isLocked)
                                bg-[var(--muted)] text-[var(--text-muted)] border-[var(--border)]
                            @elseif($status === 'selesai')
                                bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20
                            @else
                                bg-primary/10 text-primary border-primary/20
                            @endif">
                            @if($isLocked)
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            @elseif($status === 'selesai')
                                <i data-lucide="check" class="w-5 h-5"></i>
                            @else
                                <span>{{ $modul->urutan }}</span>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if($isLocked)
                                        bg-zinc-500/10 text-zinc-500 border border-zinc-500/20
                                    @elseif($status === 'selesai')
                                        bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20
                                    @else
                                        bg-primary/10 text-primary border border-primary/20
                                    @endif">
                                    Modul {{ $modul->urutan }}
                                </span>

                                <span class="text-xs text-[var(--text-secondary)] inline-flex items-center gap-1">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    {{ $total }} Materi Pelajaran
                                </span>
                            </div>

                            <h3 class="font-bold text-lg text-[var(--text-primary)]">
                                {{ $modul->judul }}
                            </h3>
                            <p class="text-sm text-[var(--text-secondary)] line-clamp-2 max-w-2xl leading-relaxed">
                                {{ $modul->deskripsi }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="shrink-0 self-end sm:self-center">
                        @if($isLocked)
                            <button disabled class="btn btn-secondary text-[var(--text-muted)] text-xs font-medium py-2.5 px-4 rounded-xl cursor-not-allowed inline-flex items-center gap-2">
                                <i data-lucide="lock" class="w-3.5 h-3.5"></i> Terkunci
                            </button>
                        @elseif($status === 'selesai')
                            <a href="{{ route('peserta.pembelajaran.show', $modul->id) }}" class="btn btn-secondary text-emerald-600 dark:text-emerald-400 text-xs font-medium py-2.5 px-4 rounded-xl inline-flex items-center gap-2 transition-all">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Selesai Dipelajari
                            </a>
                        @else
                            <a href="{{ route('peserta.pembelajaran.show', $modul->id) }}" class="btn btn-primary text-white text-xs font-medium py-2.5 px-5 rounded-xl shadow-xs inline-flex items-center gap-2 transition-all">
                                <span>Mulai Pelajari</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-16 text-center shadow-xs">
                <i data-lucide="book-open" class="w-12 h-12 text-[var(--text-muted)] mx-auto mb-3"></i>
                <h3 class="font-bold text-lg text-[var(--text-primary)] mb-1">Belum Ada Modul Tersedia</h3>
                <p class="text-sm text-[var(--text-secondary)]">Silakan periksa kembali nanti atau hubungi pengelola pelatihan.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
