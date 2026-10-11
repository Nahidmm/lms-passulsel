@extends('layouts.app')
@section('title', 'Hasil Evaluasi Pembelajaran')

@section('content')
<div class="space-y-6 py-2">

    {{-- Top Back Link --}}
    @if($materi->pelatihan_id)
        <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[var(--text-secondary)] hover:text-indigo-600 transition-colors">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Kurikulum Pelatihan
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[var(--text-secondary)] hover:text-indigo-600 transition-colors">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Dashboard
        </a>
    @endif

    <div class="max-w-xl mx-auto">

        {{-- Score Overview Card (Sidebar) --}}
        <div>
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 md:p-8 text-center sticky top-24 shadow-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-[var(--text-secondary)] mb-4">
                    <i data-lucide="award" class="w-3.5 h-3.5 text-indigo-600"></i> Rekap Evaluasi
                </span>

                <p class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-4">Skor Pencapaian</p>

                {{-- Circular Score Indicator --}}
                <div class="relative w-36 h-36 mx-auto mb-5">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" class="text-slate-100 dark:text-slate-800" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none"
                            stroke="{{ $sesi->skor >= ($materi->passing_grade ?? 70) ? '#10b981' : '#f43f5e' }}"
                            stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ 2 * pi() * 52 }}"
                            stroke-dashoffset="{{ 2 * pi() * 52 * (1 - $sesi->skor / 100) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-extrabold {{ $sesi->skor >= ($materi->passing_grade ?? 70) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ number_format((float)$sesi->skor, 0) }}
                        </span>
                        <span class="text-[11px] text-[var(--text-muted)] font-semibold">dari 100</span>
                    </div>
                </div>

                @if($sesi->skor >= ($materi->passing_grade ?? 70))
                    <div class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300 font-bold text-base mb-1">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Lulus Evaluasi
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
                        Selamat! Anda telah melampaui standar kelulusan minimum ({{ $materi->passing_grade ?? 70 }}).
                    </p>
                @else
                    <div class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-bold text-base mb-1">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i> Belum Memenuhi Kriteria
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
                        Nilai belum mencapai passing grade minimum ({{ $materi->passing_grade ?? 70 }}). Silakan kaji ulang materi.
                    </p>
                @endif

                <div class="mt-6 pt-5 border-t border-[var(--border)] grid grid-cols-2 gap-3 text-left">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                        <span class="text-[10px] uppercase font-bold text-[var(--text-secondary)] block">Jawaban Benar</span>
                        <span class="text-base font-extrabold text-[var(--text-primary)] mt-0.5 block">
                            {{ $sesi->benar }} <span class="text-xs font-normal text-[var(--text-muted)]">/ {{ $sesi->total_soal }} soal</span>
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                        <span class="text-[10px] uppercase font-bold text-[var(--text-secondary)] block">Waktu Selesai</span>
                        <span class="text-xs font-bold text-[var(--text-primary)] mt-1.5 block truncate">
                            {{ $sesi->selesai_at ? $sesi->selesai_at->format('d M, H:i') : '-' }}
                        </span>
                    </div>
                </div>

                @if($materi->pelatihan_id)
                    <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="btn btn-primary text-xs w-full justify-center mt-6">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Lanjut ke Kurikulum
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary text-xs w-full justify-center mt-6">
                        <i data-lucide="home" class="w-3.5 h-3.5"></i> Kembali ke Beranda
                    </a>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
