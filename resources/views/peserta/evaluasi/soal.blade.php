@extends('layouts.evaluasi')

@section('title', 'Kuis: ' . $materi->judul)

@php
    $isPractice = ($materi->sub_mode ?? '') === 'practice';
    $isTimeAttack = ($materi->sub_mode ?? '') === 'time_attack';
    $timerPerSoal = ($materi->timer_per_soal ?? 0) > 0 ? (int)$materi->timer_per_soal : 0;
    $totalSoal = $soals->count();
    $sisaDetik = isset($sesi->sisaWaktu) ? (int)$sesi->sisaWaktu : (($materi->durasi_menit ?? 0) * 60);
    if ($sisaDetik <= 0 && ($materi->durasi_menit ?? 0) > 0) $sisaDetik = $materi->durasi_menit * 60;
@endphp

@push('styles')
<style>
    /* Moodle Authentic Theme Styles */
    .moodle-option {
        transition: all 0.15s ease-in-out;
    }
    .moodle-option:hover {
        background-color: rgba(241, 245, 249, 0.7);
    }
    .dark .moodle-option:hover {
        background-color: rgba(30, 41, 59, 0.7);
    }
    /* When radio/checkbox is checked */
    .moodle-option:has(input:checked) {
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px #3b82f6;
    }
    .dark .moodle-option:has(input:checked) {
        background-color: rgba(30, 58, 138, 0.25) !important;
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 1px #60a5fa;
    }

    /* Moodle Question Map Tile */
    .moodle-nav-tile {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        width: 44px;
        height: 48px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background-color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.15s;
    }
    .dark .moodle-nav-tile {
        border-color: #334155;
        background-color: #0f172a;
        color: #cbd5e1;
    }
    .moodle-nav-tile:hover {
        border-color: #3b82f6;
        color: #1d4ed8;
    }
    .dark .moodle-nav-tile:hover {
        border-color: #60a5fa;
        color: #93c5fd;
    }
    .moodle-nav-tile .tile-number {
        padding-top: 5px;
    }
    .moodle-nav-tile .tile-status-bar {
        width: 100%;
        height: 16px;
        background-color: #f1f5f9;
        border-top: 1px solid #e2e8f0;
        transition: background-color 0.15s;
    }
    .dark .moodle-nav-tile .tile-status-bar {
        background-color: #1e293b;
        border-top-color: #334155;
    }
    /* Answered State (Moodle dark half-fill) */
    .moodle-nav-tile.is-answered .tile-status-bar {
        background-color: #64748b;
    }
    .dark .moodle-nav-tile.is-answered .tile-status-bar {
        background-color: #94a3b8;
    }
    /* Active State (Thick outline) */
    .moodle-nav-tile.is-active {
        border: 2px solid #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }
    .dark .moodle-nav-tile.is-active {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    /* Flagged State (Red flag in corner) */
    .moodle-nav-tile.is-flagged::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 12px 12px 0;
        border-color: transparent #ef4444 transparent transparent;
        z-index: 10;
    }
</style>
@endpush

@section('content')
<div class="space-y-4">

    {{-- Breadcrumb Moodle Header --}}
    <nav class="flex items-center gap-2 text-xs text-[var(--text-secondary)] font-medium pb-2 border-b border-[var(--border)]">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
        <span>/</span>
        @if($materi->pelatihan_id)
            <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="hover:text-blue-600 transition-colors truncate max-w-xs">
                {{ $materi->pelatihan->judul ?? 'Pelatihan' }}
            </a>
            <span>/</span>
        @endif
        <span class="text-[var(--text-primary)] font-semibold truncate">{{ $materi->judul }}</span>
    </nav>

    {{-- Title & Alert Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[var(--card)] border border-[var(--border)] rounded-xl p-4 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                    <i data-lucide="clipboard-check" class="w-3 h-3 inline mr-1"></i> Evaluasi Pembelajaran
                </span>
                @if($isPractice)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200">
                        Practice Mode
                    </span>
                @endif
                @if($isTimeAttack)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200">
                        Time Attack
                    </span>
                @endif
            </div>
            <h1 class="text-lg md:text-xl font-bold text-[var(--text-primary)] tracking-tight">{{ $materi->judul }}</h1>
        </div>

        @if($sesi->id === 'preview')
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs font-semibold">
                    <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0"></i>
                    <span>Mode Simulasi Preview (Admin)</span>
                </div>
                <a href="{{ route('admin.materi.edit', $materi->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Editor Kuis</span>
                </a>
            </div>
        @endif
    </div>

    @if($totalSoal === 0)
        <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-12 text-center max-w-xl mx-auto space-y-4 shadow-xs">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 flex items-center justify-center mx-auto text-amber-600">
                <i data-lucide="help-circle" class="w-8 h-8"></i>
            </div>
            <h2 class="text-lg font-bold text-[var(--text-primary)]">Belum Ada Soal pada Kuis Ini</h2>
            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
                Kuis ini belum memiliki daftar butir pertanyaan aktif. Silakan tambahkan butir soal terlebih dahulu melalui panel editor admin.
            </p>
            <div class="pt-3">
                @if($sesi->id === 'preview')
                    <a href="{{ route('admin.materi.edit', $materi->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Buka Editor & Tambah Soal</span>
                    </a>
                @else
                    <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Kembali ke Pelatihan</span>
                    </a>
                @endif
            </div>
        </div>
    @else
    {{-- Main Moodle Quiz 2-Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ── LEFT / MAIN: QUESTION ATTEMPT AREA (cols 8 / 9) ── --}}
        <div class="lg:col-span-8 xl:col-span-9 space-y-6">

            <form id="quiz-form" action="{{ $sesi->id === 'preview' ? route('admin.materi.preview-quiz', $materi->id) : route('peserta.evaluasi.submit', $sesi->id) }}" method="POST" onsubmit="clearSavedAnswers()">
                @csrf

                @foreach($soals as $index => $soal)
                    @php $no = $index + 1; @endphp
                    <div class="soal-step {{ $index === 0 ? '' : 'hidden' }}" id="soal-step-{{ $no }}" data-index="{{ $no }}" data-id="{{ $soal->id }}" data-tipe="{{ $soal->tipe }}">
                        
                        {{-- Moodle Question Container (Side info + formulation box) --}}
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                            {{-- Moodle Info Box (Col 3 on md) --}}
                            <div class="md:col-span-3">
                                <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 text-xs space-y-3 shadow-xs">
                                    <div>
                                        <h3 class="text-sm font-extrabold text-[var(--text-primary)]">Soal {{ $no }}</h3>
                                        <div id="badge-status-{{ $no }}" class="mt-1 font-semibold text-slate-500 dark:text-slate-400">
                                            Belum dijawab
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 text-[11px] text-[var(--text-muted)]">
                                        Poin: <strong class="text-[var(--text-secondary)] font-bold">{{ number_format($soal->bobot ?? 1, 2) }}</strong>
                                    </div>

                                    {{-- Moodle Flag Question --}}
                                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                                        <button type="button" onclick="toggleFlag({{ $no }})"
                                            id="btn-flag-{{ $no }}"
                                            class="inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors font-medium">
                                            <i data-lucide="flag" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span id="txt-flag-{{ $no }}">Tandai soal ini</span>
                                        </button>
                                    </div>

                                    @if($timerPerSoal > 0)
                                        <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                                            <div class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                <span id="soal-timer-{{ $no }}">{{ $timerPerSoal }}s</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Moodle Formulation Box (Col 9 on md) --}}
                            <div class="md:col-span-9">
                                <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl p-6 md:p-7 shadow-xs space-y-6">
                                    
                                    {{-- Question Text --}}
                                    <div class="prose prose-slate dark:prose-invert max-w-none">
                                        <div class="text-base md:text-lg font-medium text-[var(--text-primary)] leading-relaxed whitespace-pre-wrap select-text">{{ $soal->pertanyaan }}</div>
                                    </div>

                                    <hr class="border-[var(--border)]">

                                    {{-- Answer Area --}}
                                    <div class="space-y-3" id="options-container-{{ $soal->id }}">
                                        
                                        {{-- 1. PILIHAN GANDA --}}
                                        @if($soal->tipe === 'pilihan_ganda')
                                            <p class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">
                                                Pilih salah satu:
                                            </p>
                                            <div class="space-y-2.5">
                                                @foreach($soal->pilihanJawaban as $pilihan)
                                                    <label class="moodle-option flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 cursor-pointer shadow-2xs"
                                                           id="opt-label-{{ $pilihan->id }}">
                                                        <input type="radio" 
                                                               name="jawaban[{{ $soal->id }}]" 
                                                               value="{{ $pilihan->id }}"
                                                               onchange="onRadioChanged({{ $no }}, {{ $soal->id }}, this)"
                                                               data-correct="{{ $pilihan->is_correct ? 'true' : 'false' }}"
                                                               class="mt-1 w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500 shrink-0">
                                                        <div class="flex-1 text-sm md:text-base text-[var(--text-primary)] leading-normal">
                                                            @if(!empty($pilihan->huruf))
                                                                <span class="font-bold text-slate-700 dark:text-slate-300 mr-1.5">{{ $pilihan->huruf }}.</span>
                                                            @endif
                                                            <span>{{ $pilihan->teks }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>

                                            {{-- Moodle Signature: Clear My Choice Link --}}
                                            <div class="pt-2 text-right">
                                                <button type="button" onclick="clearChoice({{ $no }}, {{ $soal->id }})" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 underline transition-colors">
                                                    Bersihkan pilihan saya
                                                </button>
                                            </div>

                                        {{-- 2. MULTI SELECT --}}
                                        @elseif($soal->tipe === 'multi_select')
                                            <p class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">
                                                Pilih satu atau lebih jawaban yang benar:
                                            </p>
                                            <div class="space-y-2.5">
                                                @foreach($soal->pilihanJawaban as $pilihan)
                                                    <label class="moodle-option flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 cursor-pointer shadow-2xs"
                                                           id="opt-label-{{ $pilihan->id }}">
                                                        <input type="checkbox" 
                                                               name="jawaban[{{ $soal->id }}][]" 
                                                               value="{{ $pilihan->id }}"
                                                               onchange="onCheckboxChanged({{ $no }}, {{ $soal->id }})"
                                                               data-correct="{{ $pilihan->is_correct ? 'true' : 'false' }}"
                                                               class="mt-1 w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 shrink-0">
                                                        <div class="flex-1 text-sm md:text-base text-[var(--text-primary)] leading-normal">
                                                            @if(!empty($pilihan->huruf))
                                                                <span class="font-bold text-slate-700 dark:text-slate-300 mr-1.5">{{ $pilihan->huruf }}.</span>
                                                            @endif
                                                            <span>{{ $pilihan->teks }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>

                                        {{-- 3. ISIAN SINGKAT --}}
                                        @elseif($soal->tipe === 'isian_singkat')
                                            <div class="space-y-2">
                                                <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">
                                                    Jawaban:
                                                </label>
                                                <input type="text" 
                                                       name="jawaban[{{ $soal->id }}]" 
                                                       oninput="onTextChanged({{ $no }}, {{ $soal->id }}, this.value)"
                                                       placeholder="Ketik jawaban singkat di sini..." 
                                                       class="w-full max-w-lg px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                            </div>

                                        {{-- 4. ESSAY --}}
                                        @elseif($soal->tipe === 'essay')
                                            <div class="space-y-2">
                                                <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">
                                                    Uraian / Jawaban Esai:
                                                </label>
                                                <textarea name="jawaban[{{ $soal->id }}]" 
                                                          rows="6"
                                                          oninput="onTextChanged({{ $no }}, {{ $soal->id }}, this.value)"
                                                          placeholder="Tuliskan telaah atau penjelasan lengkap Anda di sini..."
                                                          class="w-full p-4 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-y transition-all"></textarea>
                                            </div>

                                        {{-- 5. MENJODOHKAN --}}
                                        @elseif($soal->tipe === 'menjodohkan')
                                            <p class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider mb-2">
                                                Pasangkan pernyataan berikut dengan benar:
                                            </p>
                                            <div class="space-y-3">
                                                @foreach($soal->pilihanJawaban as $pilihan)
                                                    @php $parts = explode('|||', $pilihan->teks); @endphp
                                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-lg">
                                                        <div class="flex-1 text-sm font-semibold text-[var(--text-primary)]">
                                                            {{ $parts[0] ?? $pilihan->teks }}
                                                        </div>
                                                        <i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 hidden sm:block shrink-0"></i>
                                                        <div class="flex-1">
                                                            <input type="text" 
                                                                   name="jawaban[{{ $soal->id }}][{{ $pilihan->id }}]"
                                                                   oninput="onMatchingChanged({{ $no }}, {{ $soal->id }})"
                                                                   placeholder="Pasangan jawaban..."
                                                                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-blue-500 outline-none">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Practice Mode Check Button --}}
                                    @if($isPractice && in_array($soal->tipe, ['pilihan_ganda', 'multi_select']))
                                        <div class="pt-4 border-t border-[var(--border)] flex items-center justify-between gap-3">
                                            <button type="button" onclick="checkPracticeAnswer({{ $soal->id }}, '{{ $soal->tipe }}')" 
                                                    class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-[var(--text-primary)] rounded-lg text-xs font-bold transition-all border border-slate-300 dark:border-slate-700">
                                                Periksa Jawaban
                                            </button>
                                            <div id="practice-feedback-{{ $soal->id }}" class="hidden text-xs font-bold px-3 py-1.5 rounded-lg"></div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach

                {{-- Navigation Buttons Bar (Moodle Style) --}}
                <div class="mt-6 bg-[var(--card)] border border-[var(--border)] rounded-xl p-4 flex items-center justify-between gap-4 shadow-xs">
                    <button type="button" id="btn-prev" onclick="prevSoal()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-semibold text-[var(--text-primary)] hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Halaman sebelumnya</span>
                    </button>

                    <div class="flex-1"></div>

                    <button type="button" id="btn-next" onclick="nextSoal()"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all">
                        <span>Halaman selanjutnya</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>

                    {{-- Finish Attempt Button (Visible on last question, or toggled) --}}
                    <button type="button" id="btn-finish-attempt" onclick="confirmSubmit()"
                            class="hidden inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-sm transition-all">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Selesaikan ujian...</span>
                    </button>
                </div>

            </form>

        </div>

        {{-- ── RIGHT / SIDEBAR: MOODLE QUIZ NAVIGATION (cols 4 / 3) ── --}}
        <div class="lg:col-span-4 xl:col-span-3 space-y-4">

            <div class="bg-[var(--card)] border border-[var(--border)] rounded-xl overflow-hidden shadow-xs sticky top-20">
                
                {{-- Box Header --}}
                <div class="p-4 border-b border-[var(--border)] bg-slate-50/70 dark:bg-slate-900/70 flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[var(--text-primary)] flex items-center gap-2">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-blue-600"></i>
                        Navigasi Kuis
                    </h2>

                    @if($materi->sound_enabled)
                        <button type="button" onclick="toggleSound()" id="sound-btn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" title="Beralih Suara">
                            <i data-lucide="volume-2" class="w-4 h-4"></i>
                        </button>
                    @endif
                </div>

                <div class="p-4 space-y-5">

                    {{-- User Badge --}}
                    <div class="flex items-center gap-3 pb-3 border-b border-[var(--border)]">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                            {{ strtoupper(substr(Auth::user()->nama ?? 'P', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[var(--text-primary)] truncate">{{ Auth::user()->nama }}</p>
                            <p class="text-[11px] text-[var(--text-secondary)] truncate">NIP: {{ Auth::user()->nip ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Dedicated Timer Box --}}
                    @if(($materi->durasi_menit ?? 0) > 0)
                        <div id="side-timer-card" class="p-3 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/60 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-semibold text-blue-900 dark:text-blue-200">
                                <i data-lucide="timer" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                <span>Waktu tersisa</span>
                            </div>
                            <div id="side-timer-val" class="font-mono text-base font-extrabold text-blue-700 dark:text-blue-300">
                                {{ gmdate('i:s', $sisaDetik) }}
                            </div>
                        </div>
                    @endif

                    {{-- Question Tiles Grid (Moodle Style) --}}
                    <div>
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[11px] font-bold text-[var(--text-secondary)] uppercase tracking-wider">Daftar Soal</span>
                            <span id="stat-answered-count" class="text-xs font-semibold text-slate-500">0/{{ $totalSoal }} Terjawab</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2" id="moodle-nav-grid">
                            @foreach($soals as $index => $soal)
                                @php $num = $index + 1; @endphp
                                <button type="button" 
                                        onclick="goToSoal({{ $num }})" 
                                        id="moodle-tile-{{ $num }}" 
                                        class="moodle-nav-tile {{ $index === 0 ? 'is-active' : '' }}"
                                        title="Soal {{ $num }}">
                                    <span class="tile-number">{{ $num }}</span>
                                    <span class="tile-status-bar"></span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Finish Attempt Link (Moodle Standard) --}}
                    <div class="pt-3 border-t border-[var(--border)]">
                        <button type="button" onclick="confirmSubmit()" 
                                class="w-full py-2.5 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Selesaikan ujian...</span>
                        </button>
                    </div>

                    {{-- Moodle Legend --}}
                    <div class="pt-3 border-t border-[var(--border)] space-y-2 text-[11px] text-[var(--text-secondary)]">
                        <div class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 rounded border-2 border-blue-600 bg-white dark:bg-slate-900"></div>
                            <span>Soal yang sedang dibuka</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 rounded border border-slate-300 bg-slate-500"></div>
                            <span>Telah dijawab</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 rounded border border-slate-300 bg-white dark:bg-slate-900"></div>
                            <span>Belum dijawab</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-0 h-0 border-t-8 border-t-red-500 border-r-8 border-r-transparent"></div>
                            <span>Ditandai / Ragu-ragu</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // State Variables
    const totalSoal = {{ $totalSoal }};
    let currentSoal = 1;
    const isPractice = {{ $isPractice ? 'true' : 'false' }};
    const soundEnabled = {{ ($materi->sound_enabled ?? false) ? 'true' : 'false' }};
    const timerPerSoalLimit = {{ $timerPerSoal }};
    let soundMuted = false;
    let flaggedSoals = {};
    let soalTimers = {};
    let currentSoalInterval = null;

    // Storage Key
    const storageKey = `moodle_quiz_{{ $sesi->id === 'preview' ? 'preview_' . $materi->id : $sesi->id }}`;

    document.addEventListener('DOMContentLoaded', () => {
        initView();
        loadSavedAnswers();
        initTimer();

        // Prevent unintentional submission when hitting Enter in text inputs
        const formEl = document.getElementById('quiz-form');
        if (formEl) {
            formEl.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    return false;
                }
            });
        }

        if (window.lucide) lucide.createIcons();
    });

    // ── Navigation Functions ──
    function updateView() {
        for (let i = 1; i <= totalSoal; i++) {
            const el = document.getElementById(`soal-step-${i}`);
            if (el) el.classList.toggle('hidden', i !== currentSoal);
            
            const tile = document.getElementById(`moodle-tile-${i}`);
            if (tile) tile.classList.toggle('is-active', i === currentSoal);
        }

        // Prev button: hidden on 1
        const btnPrev = document.getElementById('btn-prev');
        if (btnPrev) btnPrev.classList.toggle('hidden', currentSoal === 1);

        // Next button: hidden on last
        const btnNext = document.getElementById('btn-next');
        if (btnNext) btnNext.classList.toggle('hidden', currentSoal === totalSoal);

        // Finish button: visible on last question
        const btnFinish = document.getElementById('btn-finish-attempt');
        if (btnFinish) btnFinish.classList.toggle('hidden', currentSoal !== totalSoal);

        // Handle timer per soal
        startSoalTimer(currentSoal);
    }

    function goToSoal(num) {
        if (num >= 1 && num <= totalSoal) {
            currentSoal = num;
            updateView();
            playAudioBeep(600, 0.03);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function nextSoal() {
        if (currentSoal < totalSoal) {
            goToSoal(currentSoal + 1);
        }
    }

    function prevSoal() {
        if (currentSoal > 1) {
            goToSoal(currentSoal - 1);
        }
    }

    function initView() {
        updateView();
    }

    // ── Flagging Feature (Moodle Style) ──
    function toggleFlag(num) {
        flaggedSoals[num] = !flaggedSoals[num];
        const btn = document.getElementById(`btn-flag-${num}`);
        const txt = document.getElementById(`txt-flag-${num}`);
        const tile = document.getElementById(`moodle-tile-${num}`);

        if (flaggedSoals[num]) {
            if (txt) txt.textContent = 'Hapus tanda soal ini';
            if (btn) btn.classList.add('text-rose-600', 'font-bold');
            if (tile) tile.classList.add('is-flagged');
        } else {
            if (txt) txt.textContent = 'Tandai soal ini';
            if (btn) btn.classList.remove('text-rose-600', 'font-bold');
            if (tile) tile.classList.remove('is-flagged');
        }

        saveAnswersToStorage();
    }

    // ── Answer Change Handlers ──
    function onRadioChanged(no, soalId, radioEl) {
        setSoalStatus(no, true);
        saveAnswersToStorage();
        playAudioBeep(800, 0.03);
    }

    function onCheckboxChanged(no, soalId) {
        const step = document.getElementById(`soal-step-${no}`);
        const checked = step ? step.querySelectorAll('input[type="checkbox"]:checked').length > 0 : false;
        setSoalStatus(no, checked);
        saveAnswersToStorage();
        playAudioBeep(800, 0.03);
    }

    function onTextChanged(no, soalId, text) {
        const hasText = (text || '').trim().length > 0;
        setSoalStatus(no, hasText);
        saveAnswersToStorage();
    }

    function onMatchingChanged(no, soalId) {
        const step = document.getElementById(`soal-step-${no}`);
        let anyFilled = false;
        if (step) {
            step.querySelectorAll('input[type="text"]').forEach(input => {
                if ((input.value || '').trim().length > 0) anyFilled = true;
            });
        }
        setSoalStatus(no, anyFilled);
        saveAnswersToStorage();
    }

    function clearChoice(no, soalId) {
        const step = document.getElementById(`soal-step-${no}`);
        if (!step) return;
        step.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);
        setSoalStatus(no, false);
        saveAnswersToStorage();
    }

    function setSoalStatus(no, isAnswered) {
        const badge = document.getElementById(`badge-status-${no}`);
        const tile = document.getElementById(`moodle-tile-${no}`);

        if (isAnswered) {
            if (badge) {
                badge.textContent = 'Telah dijawab';
                badge.className = 'mt-1 font-bold text-emerald-600 dark:text-emerald-400';
            }
            if (tile) tile.classList.add('is-answered');
        } else {
            if (badge) {
                badge.textContent = 'Belum dijawab';
                badge.className = 'mt-1 font-semibold text-slate-500 dark:text-slate-400';
            }
            if (tile) tile.classList.remove('is-answered');
        }

        updateAnsweredCount();
    }

    function updateAnsweredCount() {
        const answeredCount = document.querySelectorAll('.moodle-nav-tile.is-answered').length;
        const countEl = document.getElementById('stat-answered-count');
        if (countEl) countEl.textContent = `${answeredCount}/${totalSoal} Terjawab`;
    }

    // ── LocalStorage Auto-Save & Safe Restore ──
    function saveAnswersToStorage() {
        try {
            const form = document.getElementById('quiz-form');
            if (!form) return;
            const data = {
                answers: {},
                flagged: flaggedSoals
            };

            const elements = form.elements;
            for (let i = 0; i < elements.length; i++) {
                const el = elements[i];
                if (!el.name || el.name === '_token') continue;

                if (el.type === 'radio') {
                    if (el.checked) data.answers[el.name] = el.value;
                } else if (el.type === 'checkbox') {
                    if (!data.answers[el.name]) data.answers[el.name] = [];
                    if (el.checked) data.answers[el.name].push(el.value);
                } else if (el.type === 'text' || el.tagName === 'TEXTAREA') {
                    if ((el.value || '').trim()) {
                        data.answers[el.name] = el.value;
                    }
                }
            }

            localStorage.setItem(storageKey, JSON.stringify(data));
        } catch (e) {
            console.error('Error saving answers to localStorage:', e);
        }
    }

    function loadSavedAnswers() {
        try {
            const raw = localStorage.getItem(storageKey);
            if (!raw) return;
            const data = JSON.parse(raw);
            const form = document.getElementById('quiz-form');
            if (!form) return;

            // Restore Flags
            if (data.flagged) {
                for (const num in data.flagged) {
                    if (data.flagged[num]) toggleFlag(parseInt(num));
                }
            }

            // Restore Answers safely without CSS selector syntax crash
            if (data.answers) {
                const elements = form.elements;
                for (let i = 0; i < elements.length; i++) {
                    const el = elements[i];
                    if (!el.name || el.name === '_token') continue;

                    if (data.answers[el.name] !== undefined) {
                        const val = data.answers[el.name];
                        if (el.type === 'radio') {
                            if (el.value === val) el.checked = true;
                        } else if (el.type === 'checkbox') {
                            if (Array.isArray(val) && val.includes(el.value)) el.checked = true;
                        } else if (el.type === 'text' || el.tagName === 'TEXTAREA') {
                            el.value = val;
                        }
                    }
                }
            }

            // Re-evaluate answered status for all questions
            for (let no = 1; no <= totalSoal; no++) {
                const step = document.getElementById(`soal-step-${no}`);
                if (!step) continue;
                const tipe = step.dataset.tipe;
                let answered = false;

                if (tipe === 'pilihan_ganda') {
                    answered = step.querySelectorAll('input[type="radio"]:checked').length > 0;
                } else if (tipe === 'multi_select') {
                    answered = step.querySelectorAll('input[type="checkbox"]:checked').length > 0;
                } else if (tipe === 'isian_singkat' || tipe === 'essay') {
                    const inp = step.querySelector('input[type="text"], textarea');
                    answered = inp && (inp.value || '').trim().length > 0;
                } else if (tipe === 'menjodohkan') {
                    step.querySelectorAll('input[type="text"]').forEach(i => {
                        if ((i.value || '').trim().length > 0) answered = true;
                    });
                }

                setSoalStatus(no, answered);
            }
        } catch (e) {
            console.error('Error loading saved answers:', e);
        }
    }

    function clearSavedAnswers() {
        try {
            localStorage.removeItem(storageKey);
        } catch (e) {}
    }

    // ── Global Countdown Timer ──
    function initTimer() {
        @if(($materi->durasi_menit ?? 0) > 0)
            let distance = {{ $sisaDetik }} * 1000;
            const sideTimerVal = document.getElementById('side-timer-val');
            const headerTimerVal = document.getElementById('header-timer-val');
            const sideCard = document.getElementById('side-timer-card');
            const headerBox = document.getElementById('header-timer-box');

            const timerInterval = setInterval(() => {
                distance -= 1000;

                if (distance <= 0) {
                    clearInterval(timerInterval);
                    if (sideTimerVal) sideTimerVal.textContent = '00:00';
                    if (headerTimerVal) headerTimerVal.textContent = '00:00';

                    @if($sesi->id === 'preview')
                        alert("Waktu simulasi ujian telah habis! (Mode Preview).");
                        window.location.reload();
                    @else
                        alert("Waktu pengerjaan kuis telah habis! Jawaban Anda akan dikirimkan otomatis sekarang.");
                        submitFormDirectly();
                    @endif
                    return;
                }

                const m = Math.floor(distance / 60000);
                const s = Math.floor((distance % 60000) / 1000);
                const formatted = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);

                if (sideTimerVal) sideTimerVal.textContent = formatted;
                if (headerTimerVal) headerTimerVal.textContent = formatted;

                // Warning when less than 5 minutes
                if (distance < 300000) {
                    if (sideCard) {
                        sideCard.className = 'p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 flex items-center justify-between animate-pulse';
                    }
                    if (sideTimerVal) sideTimerVal.className = 'font-mono text-base font-extrabold text-rose-600 dark:text-rose-400';
                }
            }, 1000);
        @endif
    }

    // ── Timer Per Soal Logic (if configured) ──
    function startSoalTimer(idx) {
        if (timerPerSoalLimit <= 0) return;
        if (currentSoalInterval) clearInterval(currentSoalInterval);

        let distance = soalTimers[idx] !== undefined ? soalTimers[idx] : timerPerSoalLimit;
        const el = document.getElementById('soal-timer-' + idx);
        if (el) el.textContent = distance + 's';

        currentSoalInterval = setInterval(() => {
            distance--;
            soalTimers[idx] = distance;
            if (el) el.textContent = distance + 's';

            if (distance <= 0) {
                clearInterval(currentSoalInterval);
                if (el) el.textContent = 'Habis';
                if (idx < totalSoal) {
                    nextSoal();
                } else {
                    confirmSubmit();
                }
            }
        }, 1000);
    }

    // ── Submit Attempt Confirmation ──
    function confirmSubmit() {
        const totalAnswered = document.querySelectorAll('.moodle-nav-tile.is-answered').length;
        const unanswered = totalSoal - totalAnswered;
        const flaggedCount = document.querySelectorAll('.moodle-nav-tile.is-flagged').length;

        let message = `Anda telah menjawab ${totalAnswered} dari ${totalSoal} butir soal.\n`;
        if (unanswered > 0) {
            message += `\nPERINGATAN: Ada ${unanswered} soal yang BELUM DIJAWAB!\n`;
        }
        if (flaggedCount > 0) {
            message += `\nCatatan: Ada ${flaggedCount} soal yang masih Anda tandai (ragu-ragu).\n`;
        }

        @if($sesi->id === 'preview')
            message += `\n[MODE SIMULASI ADMIN]\nApakah Anda ingin menyelesaikan simulasi dan melihat skor pratinjau?`;
        @else
            message += `\nApakah Anda yakin ingin menyelesaikan dan mengumpulkan kuis ini? Setelah dikirim, jawaban tidak dapat diubah lagi.`;
        @endif

        if (confirm(message)) {
            submitFormDirectly();
        }
    }

    function submitFormDirectly() {
        const form = document.getElementById('quiz-form');
        if (form) {
            form.submitted = true;
            clearSavedAnswers();
            form.submit();
        }
    }

    window.addEventListener('beforeunload', (e) => {
        const form = document.getElementById('quiz-form');
        if (form && !form.submitted && '{{ $sesi->id }}' !== 'preview') {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // ── Proctoring Anti-Cheat (if enabled) ──
    @if($materi->strict_anti_cheat && $sesi->id !== 'preview')
        let cheatWarnings = 0;
        const maxWarnings = 3;
        function onTabBlurDetected() {
            const form = document.getElementById('quiz-form');
            if (form && form.submitted) return;

            cheatWarnings++;
            playAudioBeep(250, 0.3);

            if (cheatWarnings < maxWarnings) {
                alert(`PERINGATAN INTEGRITAS (${cheatWarnings}/${maxWarnings}):\nTerdeteksi perpindahan tab atau jendela aplikasi! Harap tetap fokus pada lembar ujian.`);
            } else {
                alert(`PERINGATAN MAKSIMAL TERCAPAI:\nAnda telah berpindah tab sebanyak ${maxWarnings} kali. Ujian akan otomatis dikumpulkan sekarang.`);
                submitFormDirectly();
            }
        }

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') onTabBlurDetected();
        });
        window.addEventListener('blur', onTabBlurDetected);
    @endif

    // ── Practice Mode Immediate Feedback ──
    function checkPracticeAnswer(soalId, tipe) {
        const container = document.getElementById(`options-container-${soalId}`);
        const feedback = document.getElementById(`practice-feedback-${soalId}`);
        if (!container || !feedback) return;

        let answered = false;
        let isCorrect = false;

        if (tipe === 'pilihan_ganda') {
            const checked = container.querySelector('input[type="radio"]:checked');
            if (checked) {
                answered = true;
                isCorrect = checked.dataset.correct === 'true';
            }
        } else if (tipe === 'multi_select') {
            const checks = container.querySelectorAll('input[type="checkbox"]');
            let allCorrect = true;
            let anyChecked = false;
            checks.forEach(c => {
                if (c.checked) anyChecked = true;
                if (c.checked && c.dataset.correct !== 'true') allCorrect = false;
                if (!c.checked && c.dataset.correct === 'true') allCorrect = false;
            });
            if (anyChecked) {
                answered = true;
                isCorrect = allCorrect;
            }
        }

        if (!answered) {
            alert('Silakan pilih salah satu jawaban terlebih dahulu sebelum memeriksa.');
            return;
        }

        feedback.classList.remove('hidden', 'bg-rose-50', 'text-rose-700', 'bg-emerald-50', 'text-emerald-700');
        if (isCorrect) {
            feedback.textContent = '✓ Jawaban Anda Benar!';
            feedback.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200');
            playAudioBeep(880, 0.15);
        } else {
            feedback.textContent = '✗ Jawaban Belum Tepat. Silakan periksa kembali.';
            feedback.classList.add('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');
            playAudioBeep(300, 0.2);
        }
    }

    // ── Audio Helper ──
    let audioCtx = null;
    function playAudioBeep(freq, duration) {
        if (!soundEnabled || soundMuted) return;
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);

            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + duration);
        } catch (e) {}
    }

    function toggleSound() {
        soundMuted = !soundMuted;
        const btn = document.getElementById('sound-btn');
        if (btn) {
            btn.innerHTML = soundMuted ? '<i data-lucide="volume-x" class="w-4 h-4 text-rose-500"></i>' : '<i data-lucide="volume-2" class="w-4 h-4"></i>';
            if (window.lucide) lucide.createIcons();
        }
    }
</script>
@endpush
