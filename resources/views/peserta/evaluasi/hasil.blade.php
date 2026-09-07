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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Score Overview Card (Sidebar) --}}
        <div class="lg:col-span-1">
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

        {{-- Question Discussion / Pembahasan --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
                <div class="p-5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-[var(--text-primary)]">Pembahasan Jawaban</h3>
                        <p class="text-xs text-[var(--text-secondary)]">Koreksi lembar jawaban dan ulasan materi regulasi terkait</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[var(--text-secondary)]">
                        {{ $hasilLatihans->count() }} Pertanyaan
                    </span>
                </div>

                <div class="p-5 md:p-6 divide-y divide-[var(--border)] space-y-6">
                    @foreach($hasilLatihans as $index => $hasil)
                        <div class="pt-6 first:pt-0">
                            <div class="flex items-start gap-3.5">
                                {{-- Correct / Incorrect icon --}}
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 mt-0.5
                                    {{ $hasil->is_correct ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                    @if($hasil->is_correct)
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="text-xs font-bold text-[var(--text-secondary)]">Soal No. {{ $index + 1 }}</span>
                                        @if($hasil->is_correct)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Benar</span>
                                        @else
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">Salah</span>
                                        @endif
                                    </div>

                                    <h4 class="text-sm font-semibold text-[var(--text-primary)] leading-relaxed mb-4">
                                        {{ $hasil->soal->pertanyaan }}
                                    </h4>

                                    <div class="space-y-2 mb-4">
                                        @foreach($hasil->soal->pilihanJawaban as $pilihan)
                                            @php
                                                $isUserChoice    = $hasil->pilihan_id == $pilihan->id;
                                                $isCorrectChoice = $pilihan->is_correct;

                                                $rowStyle = 'bg-slate-50/50 dark:bg-slate-900/50 border-[var(--border)] text-[var(--text-secondary)]';
                                                if ($isCorrectChoice) {
                                                    $rowStyle = 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 font-semibold';
                                                } elseif ($isUserChoice && !$isCorrectChoice) {
                                                    $rowStyle = 'bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200 font-semibold';
                                                }
                                            @endphp
                                            <div class="flex items-center justify-between p-3 rounded-xl border text-xs leading-relaxed {{ $rowStyle }}">
                                                <div class="flex items-start gap-2.5 flex-1 pr-3">
                                                    <span class="font-bold w-4 shrink-0">{{ $pilihan->huruf }}.</span>
                                                    <span>{{ $pilihan->teks }}</span>
                                                </div>
                                                <div class="shrink-0 flex items-center gap-1.5">
                                                    @if($isUserChoice)
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isCorrectChoice ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                            Jawaban Anda
                                                        </span>
                                                    @endif
                                                    @if($isCorrectChoice && !$isUserChoice)
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                            Kunci Jawaban
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($hasil->soal->pembahasan)
                                        <div class="rounded-xl border border-indigo-200 dark:border-indigo-900 bg-indigo-50/50 dark:bg-indigo-950/20 p-3.5">
                                            <p class="text-[11px] font-bold text-indigo-700 dark:text-indigo-300 mb-1 flex items-center gap-1.5 uppercase tracking-wider">
                                                <i data-lucide="info" class="w-3.5 h-3.5"></i> Pembahasan Regulasi
                                            </p>
                                            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
                                                {{ $hasil->soal->pembahasan }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
