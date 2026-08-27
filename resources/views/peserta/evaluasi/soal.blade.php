@extends('layouts.evaluasi')

@section('content')
@php
    $themeClass = $materi->mode_tampilan === 'interaktif' ? 'quiz-theme-game' : 'quiz-theme-standard';
    $isPractice = $materi->sub_mode === 'practice';
    $isTimeAttack = $materi->sub_mode === 'time_attack';
    $timerPerSoal = $materi->timer_per_soal > 0 ? (int)$materi->timer_per_soal : 0;
@endphp

<div class="relative max-w-7xl mx-auto {{ $themeClass }}">
    <div class="quiz-container">
        
        {{-- Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ MAIN AREA Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
        <div class="quiz-main-area">
            
            {{-- INTERACTIVE HEADER --}}
            @if($materi->mode_tampilan === 'interaktif')
            <div class="mb-5 rounded-2xl border border-violet-500/20 bg-black/40 backdrop-blur p-5 relative overflow-hidden">
                <div class="absolute w-40 h-40 bg-violet-600/30 blur-[50px] -top-10 -right-10 rounded-full"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 bg-violet-900/40 border border-violet-500/30 text-violet-300 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full mb-2">
                            <i data-lucide="swords" class="w-3 h-3"></i> {{ $isTimeAttack ? 'Time Attack' : 'Quest Mode' }}
                        </span>
                        <h2 class="font-display font-black text-2xl text-[var(--text-primary)]">{{ $materi->judul }}</h2>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        @if($materi->durasi_menit > 0)
                        <div id="main-timer-box" class="flex items-center gap-2 bg-rose-500/10 border border-rose-500/20 text-rose-400 px-3 py-2 rounded-xl text-sm font-black transition-all">
                            <i data-lucide="timer" class="w-4 h-4"></i>
                            <span id="countdown-timer">{{ gmdate('i:s', $sesi->sisaWaktu) }}</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-2 bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 px-3 py-2 rounded-xl text-sm font-black">
                            <i data-lucide="target" class="w-4 h-4"></i> <span id="progress-text">1/{{ $soals->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="quiz-progress-track">
                    <div id="progress-bar" class="quiz-progress-fill" style="width:1%"></div>
                </div>
            </div>
            @endif

            <div class="quiz-card">
                {{-- Standard Header --}}
                @if($materi->mode_tampilan !== 'interaktif')
                <div class="quiz-header-bar">
                    <div>
                        <h2 class="font-bold text-lg leading-tight">{{ $materi->judul }}</h2>
                        @if($isPractice)
                        <span class="text-xs font-semibold text-[var(--quiz-primary)] bg-[var(--quiz-primary-soft)] px-2 py-0.5 rounded mt-1 inline-block">Practice Mode</span>
                        @endif
                        @if($isTimeAttack)
                        <span class="text-xs font-semibold text-[var(--quiz-danger)] bg-rose-500/10 px-2 py-0.5 rounded mt-1 inline-block">Time Attack</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @if($materi->durasi_menit > 0)
                        <div id="main-timer-box" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-[var(--quiz-border)] font-bold text-sm transition-all">
                            <i data-lucide="timer" class="w-4 h-4 text-[var(--quiz-muted)]"></i>
                            <span id="countdown-timer">{{ gmdate('i:s', $sesi->sisaWaktu) }}</span>
                        </div>
                        @endif
                        <div class="text-sm font-bold text-[var(--quiz-muted)]" id="progress-text">1/{{ $soals->count() }}</div>
                    </div>
                </div>
                @if($materi->mode_tampilan !== 'interaktif')
                <div class="quiz-progress-track rounded-none m-0 h-1">
                    <div id="progress-bar" class="quiz-progress-fill rounded-none" style="width:1%"></div>
                </div>
                @endif
                @endif

                <form id="quiz-form" action="{{ route('peserta.evaluasi.submit', $sesi->id) }}" method="POST" onsubmit="clearSavedAnswers()">
                    @csrf
                    <div class="p-6 md:p-8">
                        @foreach($soals as $index => $soal)
                            <div class="soal-item {{ $index === 0 ? '' : 'hidden' }}" id="soal-step-{{ $index+1 }}" data-index="{{ $index+1 }}" data-id="{{ $soal->id }}">
                                
                                {{-- Timer per Soal UI --}}
                                @if($timerPerSoal > 0)
                                <div class="flex justify-end mb-4">
                                    <div class="inline-flex items-center gap-1.5 text-xs font-bold text-[var(--quiz-danger)] bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/20 pulse-animation">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        <span class="soal-timer" id="soal-timer-{{ $index+1 }}">{{ $timerPerSoal }}s</span>
                                    </div>
                                </div>
                                @endif

                                <div class="flex flex-col md:flex-row gap-5">
                                    {{-- Number badge --}}
                                    <div class="shrink-0 w-10 h-10 md:w-12 md:h-12 rounded-xl bg-[var(--quiz-primary-soft)] text-[var(--quiz-primary)] flex items-center justify-center font-black text-xl">
                                        {{ $index+1 }}
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        {{-- Question text --}}
                                        <div class="font-bold text-lg md:text-xl leading-relaxed mb-6 whitespace-pre-wrap">{{ $soal->pertanyaan }}</div>

                                        {{-- Options --}}
                                        <div class="space-y-3" id="options-container-{{ $soal->id }}">
                                            @if($soal->tipe === 'pilihan_ganda' || $soal->tipe === 'multi_select')
                                                @if($index === 0)
                                                <style>
                                                    input[type="radio"]:checked ~ .radio-indicator .radio-dot {
                                                        opacity: 1;
                                                        transform: scale(1);
                                                    }
                                                    .check-indicator .check-mark {
                                                        opacity: 0;
                                                        transform: scale(0.5);
                                                        transition: all 0.2s ease;
                                                    }
                                                    input[type="checkbox"]:checked ~ .check-indicator .check-mark {
                                                        opacity: 1;
                                                        transform: scale(1);
                                                    }
                                                </style>
                                                @endif
                                            @endif

                                            @if($soal->tipe === 'pilihan_ganda')
                                                @foreach($soal->pilihanJawaban as $pilihan)
                                                    <label class="quiz-option" id="opt-{{ $pilihan->id }}">
                                                        <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $pilihan->id }}"
                                                            onchange="markAnswered({{ $index+1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                            data-correct="{{ $pilihan->is_correct ? 'true' : 'false' }}"
                                                            class="hidden peer">
                                                        <div class="radio-indicator w-5 h-5 rounded-full border-2 border-[var(--quiz-border)] peer-checked:border-[var(--quiz-primary)] flex items-center justify-center shrink-0 mt-0.5 transition-all">
                                                            <div class="radio-dot w-2.5 h-2.5 rounded-full bg-[var(--quiz-primary)] opacity-0 scale-0 transition-all duration-200"></div>
                                                        </div>
                                                        <div class="flex-1 text-base">
                                                            @if(isset($pilihan->huruf) && $pilihan->huruf)<span class="font-black text-[var(--quiz-primary)] mr-2">{{ $pilihan->huruf }}.</span>@endif
                                                            <span>{{ $pilihan->teks }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            @elseif($soal->tipe === 'multi_select')
                                                @foreach($soal->pilihanJawaban as $pilihan)
                                                    <label class="quiz-option" id="opt-{{ $pilihan->id }}">
                                                        <input type="checkbox" name="jawaban[{{ $soal->id }}][]" value="{{ $pilihan->id }}"
                                                            onchange="markAnswered({{ $index+1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                            data-correct="{{ $pilihan->is_correct ? 'true' : 'false' }}"
                                                            class="hidden peer">
                                                        <div class="check-indicator w-5 h-5 rounded border-2 border-[var(--quiz-border)] peer-checked:border-[var(--quiz-primary)] peer-checked:bg-[var(--quiz-primary)] text-white flex items-center justify-center shrink-0 mt-0.5 transition-all">
                                                            <svg class="check-mark" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                        </div>
                                                        <div class="flex-1 text-base">
                                                            @if(isset($pilihan->huruf) && $pilihan->huruf)<span class="font-black text-[var(--quiz-primary)] mr-2">{{ $pilihan->huruf }}.</span>@endif
                                                            <span>{{ $pilihan->teks }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            @elseif($soal->tipe === 'essay' || $soal->tipe === 'isian_singkat')
                                                <textarea name="jawaban[{{ $soal->id }}]"
                                                    rows="{{ $soal->tipe === 'essay' ? 5 : 2 }}"
                                                    oninput="markAnswered({{ $index+1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                    placeholder="Ketik jawaban Anda di sini..."
                                                    class="w-full bg-[var(--quiz-bg)] border border-[var(--quiz-border)] rounded-xl p-4 text-[var(--quiz-text)] focus:ring-2 focus:ring-[var(--quiz-primary)] focus:border-[var(--quiz-primary)] outline-none resize-y"></textarea>
                                            @elseif($soal->tipe === 'menjodohkan')
                                                <div class="grid grid-cols-1 gap-4">
                                                    @foreach($soal->pilihanJawaban as $pilihan)
                                                        @php $parts = explode('|||', $pilihan->teks); @endphp
                                                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                            <div class="flex-1 p-4 bg-[var(--quiz-bg)] border border-[var(--quiz-border)] rounded-xl text-sm font-bold">{{ $parts[0] ?? '' }}</div>
                                                            <i data-lucide="arrow-right" class="w-5 h-5 text-[var(--quiz-muted)] hidden sm:block shrink-0"></i>
                                                            <div class="flex-1">
                                                                <input type="text" name="jawaban[{{ $soal->id }}][{{ $pilihan->id }}]"
                                                                    oninput="markAnswered({{ $index+1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                                    placeholder="Pasangkan dengan..."
                                                                    class="w-full bg-[var(--quiz-bg)] border border-[var(--quiz-border)] rounded-xl p-4 text-[var(--quiz-text)] focus:ring-2 focus:ring-[var(--quiz-primary)] outline-none">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Practice Mode Action --}}
                                        @if($isPractice && in_array($soal->tipe, ['pilihan_ganda', 'multi_select']))
                                        <div class="mt-6 flex items-center justify-between">
                                            <button type="button" onclick="checkPracticeAnswer({{ $soal->id }}, '{{ $soal->tipe }}')" class="bg-[var(--quiz-bg)] hover:bg-[var(--quiz-border)] border border-[var(--quiz-border)] text-[var(--quiz-text)] font-semibold px-4 py-2 rounded-lg text-sm transition-colors">
                                                Cek Jawaban
                                            </button>
                                            <div id="practice-feedback-{{ $soal->id }}" class="hidden text-sm font-bold px-3 py-1.5 rounded-lg"></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Nav Footer --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-[var(--quiz-border)] bg-[var(--quiz-bg)]">
                        <button type="button" id="btn-prev" onclick="prevSoal()" class="hidden items-center gap-2 px-4 py-2 rounded-lg border border-[var(--quiz-border)] text-[var(--quiz-muted)] hover:text-[var(--quiz-text)] hover:bg-[var(--quiz-card)] font-bold text-sm transition-all">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                        </button>
                        <div class="flex-1"></div>
                        <button type="button" id="btn-next" onclick="nextSoal()" class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[var(--quiz-primary)] text-[var(--text-primary)] font-bold text-sm hover:opacity-90 shadow-lg shadow-[var(--quiz-primary-soft)] transition-all">
                            Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ SIDEBAR MAP Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ --}}
        <div class="quiz-sidebar">
            <div class="quiz-card sticky top-24">
                <div class="px-5 py-4 border-b border-[var(--quiz-border)] flex items-center justify-between gap-2 bg-[var(--quiz-bg)]">
                    <div class="flex items-center gap-2">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-[var(--quiz-primary)]"></i>
                        <span class="font-bold text-sm">Navigasi Soal</span>
                    </div>
                    <button type="button" id="mute-btn" onclick="toggleMute()" class="p-1.5 rounded-lg hover:bg-[var(--quiz-border)] text-[var(--quiz-muted)] transition-colors" title="Toggle Sound">
                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-5 md:grid-cols-6 lg:grid-cols-5 gap-2" id="question-map">
                        @foreach($soals as $index => $soal)
                            <button type="button" onclick="goToSoal({{ $index+1 }})"
                                id="map-btn-{{ $index+1 }}"
                                class="w-10 h-10 rounded-lg border-2 border-[var(--quiz-border)] flex items-center justify-center font-bold text-sm text-[var(--quiz-muted)] transition-all hover:border-[var(--quiz-primary)] hover:text-[var(--quiz-primary)]">
                                {{ $index+1 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Legend -->
                    <div class="mt-6 pt-5 border-t border-[var(--quiz-border)] space-y-3">
                        <div class="flex items-center gap-3 text-sm font-semibold text-[var(--quiz-muted)]">
                            <div class="w-5 h-5 rounded border-2 border-[var(--quiz-primary)] bg-[var(--quiz-primary-soft)]"></div> Aktif
                        </div>
                        <div class="flex items-center gap-3 text-sm font-semibold text-[var(--quiz-muted)]">
                            <div class="w-5 h-5 rounded border-2 border-[var(--quiz-success)] bg-[var(--quiz-bg)] text-[var(--quiz-success)] flex items-center justify-center"><div class="w-2.5 h-2.5 bg-[var(--quiz-success)] rounded-sm"></div></div> Terjawab
                        </div>
                        <div class="flex items-center gap-3 text-sm font-semibold text-[var(--quiz-muted)]">
                            <div class="w-5 h-5 rounded border-2 border-[var(--quiz-border)] bg-[var(--quiz-bg)]"></div> Belum
                        </div>
                    </div>
                </div>
                
                <div class="p-5 border-t border-[var(--quiz-border)] bg-[var(--quiz-bg)]">
                    <button type="button" onclick="confirmSubmit()" class="w-full py-3.5 rounded-xl bg-[var(--quiz-danger)] text-[var(--text-primary)] font-bold text-sm flex items-center justify-center gap-2 hover:opacity-90 shadow-lg shadow-rose-500/20 transition-all">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Selesaikan Kuis
                    </button>
                    <p class="text-center text-xs text-[var(--quiz-muted)] mt-3">Pastikan semua terjawab.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const totalSoal = {{ $soals->count() }};
let currentSoal = 1;
const isPractice = {{ $isPractice ? 'true' : 'false' }};
const showAnswerReview = {{ $materi->show_answer_review ? 'true' : 'false' }};
const timerPerSoalLimit = {{ $timerPerSoal }};
let soalTimers = {};

function updateProgress() {
    const pct = Math.round((currentSoal / totalSoal) * 100);
    const bar = document.getElementById('progress-bar');
    if (bar) bar.style.width = pct + '%';
    const txt = document.getElementById('progress-text');
    if(txt) txt.textContent = currentSoal + '/' + totalSoal;
}

document.addEventListener('DOMContentLoaded', () => {
    updateView();
    loadAnswers();
    updateMapStyling();
    lucide.createIcons();
    initGlobalTimer();
});

function initGlobalTimer() {
    @if($materi->durasi_menit > 0)
    let distance = {{ $sesi->sisaWaktu }} * 1000;
    const timerEl = document.getElementById("countdown-timer");
    const boxEl = document.getElementById("main-timer-box");
    const interval = setInterval(() => {
        distance -= 1000;
        if (!timerEl) return;
        if (distance <= 0) {
            clearInterval(interval);
            timerEl.textContent = '00:00';
            @if($sesi->id === 'preview')
                alert("Waktu ujian habis! (Simulasi Preview Selesai).");
                window.location.reload();
            @else
                alert("Waktu habis! Jawaban akan dikirim otomatis.");
                document.getElementById("quiz-form").submitted = true;
                document.getElementById("quiz-form").submit();
            @endif
            return;
        }
        const m = Math.floor(distance / 60000), s = Math.floor((distance % 60000) / 1000);
        timerEl.textContent = (m<10?'0'+m:m)+':'+(s<10?'0'+s:s);
        
        // Time attack / warning state
        if (distance < 60000 && boxEl) {
            boxEl.classList.add('timer-danger');
        }
    }, 1000);
    @endif
}

// Timer per soal logic
let currentSoalInterval = null;
let currentSoalDistance = timerPerSoalLimit;

function startSoalTimer(idx) {
    if (timerPerSoalLimit <= 0) return;
    if (currentSoalInterval) clearInterval(currentSoalInterval);
    
    // Check if we already spent time or if it's locked
    if (soalTimers[idx] === 'locked') {
        lockQuestion(idx);
        return;
    }
    
    currentSoalDistance = soalTimers[idx] !== undefined ? soalTimers[idx] : timerPerSoalLimit;
    const el = document.getElementById('soal-timer-' + idx);
    if (!el) return;
    el.textContent = currentSoalDistance + 's';
    
    currentSoalInterval = setInterval(() => {
        currentSoalDistance--;
        soalTimers[idx] = currentSoalDistance;
        el.textContent = currentSoalDistance + 's';
        
        if (currentSoalDistance <= 0) {
            clearInterval(currentSoalInterval);
            soalTimers[idx] = 'locked';
            lockQuestion(idx);
            
            // Mainkan suara salah/habis waktu jika ada
            if(typeof playWrongSound === 'function') playWrongSound();

            if (idx < totalSoal) {
                nextSoal();
            } else {
                // Jika soal terakhir habis waktu, otomatis kumpulkan
                @if($sesi->id === 'preview')
                    alert("Waktu habis! Kuis Preview Selesai.");
                    window.location.reload();
                @else
                    document.getElementById('quiz-form').submitted = true;
                    document.getElementById('quiz-form').submit();
                @endif
            }
        }
    }, 1000);
}

function lockQuestion(idx) {
    const container = document.getElementById(`soal-step-${idx}`);
    if(!container) return;
    
    // Jangan gunakan disabled=true agar data tetap bisa dikumpulkan oleh form
    // Gunakan trik CSS untuk menonaktifkan interaksi klik/ubah
    container.style.pointerEvents = 'none';
    container.style.opacity = '0.6';

    const el = document.getElementById('soal-timer-' + idx);
    if(el) el.textContent = 'Habis';
}

function nextSoal() { if (currentSoal < totalSoal) { currentSoal++; updateView(); playClickSound(); } }
function prevSoal() { if (currentSoal > 1) { currentSoal--; updateView(); playClickSound(); } }
function goToSoal(i) { currentSoal = i; updateView(); playClickSound(); }

function updateView() {
    for (let i = 1; i <= totalSoal; i++) {
        document.getElementById(`soal-step-${i}`).classList.add('hidden');
    }
    document.getElementById(`soal-step-${currentSoal}`).classList.remove('hidden');
    document.getElementById('btn-prev').classList.toggle('hidden', currentSoal === 1);
    document.getElementById('btn-next').classList.toggle('hidden', currentSoal === totalSoal);
    if (currentSoal > 1) document.getElementById('btn-prev').classList.remove('hidden');
    updateProgress();
    updateMapStyling();
    startSoalTimer(currentSoal);
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Practice Mode Ã¢â€â‚¬Ã¢â€â‚¬
function checkPracticeAnswer(soalId, tipe) {
    const container = document.getElementById(`options-container-${soalId}`);
    const feedback = document.getElementById(`practice-feedback-${soalId}`);
    if(!container || !feedback) return;
    
    let isCorrect = false;
    let answered = false;

    if (tipe === 'pilihan_ganda') {
        const checked = container.querySelector(`input[type="radio"]:checked`);
        if(checked) { answered = true; isCorrect = checked.dataset.correct === 'true'; }
    } else if (tipe === 'multi_select') {
        const checks = container.querySelectorAll(`input[type="checkbox"]`);
        let allCorrect = true;
        let anyChecked = false;
        checks.forEach(c => {
            if(c.checked) anyChecked = true;
            if(c.checked && c.dataset.correct !== 'true') allCorrect = false;
            if(!c.checked && c.dataset.correct === 'true') allCorrect = false;
        });
        if(anyChecked) { answered = true; isCorrect = allCorrect; }
    }
    
    if(!answered) {
        alert("Pilih jawaban terlebih dahulu."); return;
    }

    feedback.classList.remove('hidden', 'bg-rose-500/10', 'text-[var(--quiz-danger)]', 'bg-emerald-500/10', 'text-[var(--quiz-success)]');
    
    if(isCorrect) {
        playCorrectSound();
        feedback.textContent = 'Benar!';
        feedback.classList.add('bg-emerald-500/10', 'text-[var(--quiz-success)]');
    } else {
        playWrongSound();
        feedback.textContent = 'Salah. Coba lagi!';
        feedback.classList.add('bg-rose-500/10', 'text-[var(--quiz-danger)]');
    }

    if(showAnswerReview) {
        // Highlight correct options
        container.querySelectorAll('input').forEach(i => {
            const label = document.getElementById(`opt-${i.value}`);
            if(label) {
                if(i.dataset.correct === 'true') label.classList.add('is-correct');
                else if(i.checked) label.classList.add('is-wrong');
            }
        });
    }
}

// Ã¢â€â‚¬Ã¢â€â‚¬ Auto-save Ã¢â€â‚¬Ã¢â€â‚¬
const storageKey = `quiz_answers_{{ $sesi->id }}`;
function saveAnswers() {
    const fd = new FormData(document.getElementById('quiz-form'));
    const d = {};
    for (const [k,v] of fd.entries()) { if (k!=='_token') { if(!d[k]) d[k]=[]; d[k].push(v); } }
    localStorage.setItem(storageKey, JSON.stringify(d));
}
function loadAnswers() {
    const saved = localStorage.getItem(storageKey);
    if (!saved) return;
    try {
        const data = JSON.parse(saved);
        const form = document.getElementById('quiz-form');
        for (const key in data) {
            const vals = data[key];
            form.querySelectorAll(`[name="${key}"], [name="${key}[]"]`).forEach(el => {
                if (el.type==='radio'||el.type==='checkbox') { if(vals.includes(el.value)) el.checked=true; }
                else el.value = vals[0]||'';
            });
        }
        updateAnsweredStatus();
    } catch(e) {}
}
function clearSavedAnswers() { localStorage.removeItem(storageKey); }

function markAnswered(idx, soalId, tipe) { 
    // Handle visual selection
    if(tipe === 'pilihan_ganda' || tipe === 'multi_select') {
        const container = document.getElementById(`options-container-${soalId}`);
        container.querySelectorAll('.quiz-option').forEach(l => l.classList.remove('selected'));
        
        container.querySelectorAll('input:checked').forEach(i => {
            const lbl = document.getElementById(`opt-${i.value}`);
            if(lbl) lbl.classList.add('selected');
        });
    }
    
    checkQuestion(idx, soalId, tipe); 
    saveAnswers(); 
}

function updateAnsweredStatus() {
    @foreach($soals as $i => $s)
        markAnswered({{ $i+1 }}, {{ $s->id }}, '{{ $s->tipe }}');
    @endforeach
}

function checkQuestion(idx, soalId, tipe) {
    let answered = false;
    const form = document.getElementById('quiz-form');
    if (tipe==='pilihan_ganda'||tipe==='multi_select') {
        answered = !!form.querySelector(`input[name^="jawaban[${soalId}]"]:checked`);
    } else if (tipe==='essay'||tipe==='isian_singkat') {
        answered = (form.querySelector(`textarea[name="jawaban[${soalId}]"]`)?.value||'').trim().length > 0;
    } else if (tipe==='menjodohkan') {
        form.querySelectorAll(`input[name^="jawaban[${soalId}]"]`).forEach(i => { if(i.value.trim()) answered=true; });
    }
    const btn = document.getElementById(`map-btn-${idx}`);
    if (btn) btn.dataset.answered = answered ? 'true' : 'false';
    updateMapStyling();
}

function updateMapStyling() {
    for (let i = 1; i <= totalSoal; i++) {
        const btn = document.getElementById(`map-btn-${i}`);
        if (!btn) continue;
        const answered = btn.dataset.answered === 'true';
        const active = i === currentSoal;
        
        btn.className = `w-10 h-10 rounded-lg border-2 flex items-center justify-center font-bold text-sm transition-all ${
            active ? 'border-[var(--quiz-primary)] bg-[var(--quiz-primary-soft)] text-[var(--quiz-primary)]' :
            answered ? 'border-[var(--quiz-success)] text-[var(--quiz-success)]' :
            'border-[var(--quiz-border)] text-[var(--quiz-muted)] hover:border-[var(--quiz-primary)] hover:text-[var(--quiz-primary)]'
        }`;
    }
}

// â”€â”€ Submit â”€â”€
function confirmSubmit() {
    @if($sesi->id === 'preview')
        alert("Selesai! Ini hanya simulasi Preview dari Admin, sehingga jawaban tidak dikirim ke server.");
        window.location.reload();
        return;
    @endif
    let unanswered = 0;
    for (let i=1; i<=totalSoal; i++) {
        if (document.getElementById(`map-btn-${i}`).dataset.answered !== 'true') unanswered++;
    }
    let msg = 'Apakah Anda yakin ingin menyelesaikan kuis ini? Jawaban tidak dapat diubah lagi.';
    if (unanswered > 0) msg = `PERINGATAN: Ada ${unanswered} soal yang belum dijawab!\n\n` + msg;
    if (confirm(msg)) {
        document.getElementById('quiz-form').submitted = true;
        document.getElementById('quiz-form').submit();
    }
}
window.addEventListener('beforeunload', e => {
    if (!document.getElementById('quiz-form').submitted) { e.preventDefault(); e.returnValue=''; }
});

@if($materi->strict_anti_cheat)
let warnings = 0;
const maxWarn = 2;
function handleCheat() {
    if(document.getElementById('quiz-form').submitted) return;
    warnings++;
    if(warnings <= maxWarn) {
        if(typeof playWrongSound === 'function') playWrongSound();
        alert(`Peringatan (${warnings}/${maxWarn}): Anda terdeteksi berpindah aplikasi/tab! Lakukan tes dengan jujur.`);
    } else {
        @if($sesi->id === 'preview')
            alert("Terdeteksi pelanggaran (Mode Preview). Halaman akan dimuat ulang.");
            window.location.reload();
        @else
            document.getElementById('quiz-form').submitted = true;
            document.getElementById('quiz-form').submit();
        @endif
    }
}
document.addEventListener('visibilitychange', () => { if (document.visibilityState==='hidden') handleCheat(); });
window.addEventListener('blur', handleCheat);
@endif

// Ã¢â€â‚¬Ã¢â€â‚¬ Sound Engine Ã¢â€â‚¬Ã¢â€â‚¬
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
let isMuted = false;
@if($materi->mode_tampilan === 'interaktif')
    const bgmAudio = new Audio('{{ asset("music/electric.mp3") }}');
    bgmAudio.loop = true;
    bgmAudio.volume = 0.5; // Sesuaikan volume musik Electric

    const startBGM = () => {
        if(isMuted) return;
        bgmAudio.play().catch(e => console.log('Autoplay blocked', e));
    };

    const stopBGM = () => {
        bgmAudio.pause();
    };

    const initAudio = () => {
        if(bgmAudio.paused && !isMuted) startBGM();
        if(audioCtx.state === 'suspended') audioCtx.resume();
        // Hapus listener setelah terpanggil sekali
        window.removeEventListener('click', initAudio);
        window.removeEventListener('touchstart', initAudio);
        window.removeEventListener('keydown', initAudio);
    };

    window.addEventListener('click', initAudio);
    window.addEventListener('touchstart', initAudio);
    window.addEventListener('keydown', initAudio);
@else
    const startBGM = () => {};
    const stopBGM = () => {};
@endif

function toggleMute() {
    isMuted = !isMuted;
    const btn = document.getElementById('mute-btn');
    if(btn) {
        btn.innerHTML = isMuted ? '<i data-lucide="volume-x" class="w-4 h-4"></i>' : '<i data-lucide="volume-2" class="w-4 h-4"></i>';
        if(window.lucide) window.lucide.createIcons();
    }
    
    if(isMuted) stopBGM();
    else startBGM();
}

const playTone = (freq, type, duration, vol=0.1) => {
    if(isMuted) return;
    try {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
        gain.gain.setValueAtTime(vol, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + duration);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + duration);
    } catch(e) {}
};

const playCorrectSound = () => {
    if(audioCtx.state === 'suspended') audioCtx.resume();
    playTone(523.25, 'sine', 0.1, 0.2); 
    setTimeout(() => playTone(659.25, 'sine', 0.2, 0.2), 100); 
    setTimeout(() => playTone(783.99, 'sine', 0.4, 0.3), 200); 
};

const playWrongSound = () => {
    if(audioCtx.state === 'suspended') audioCtx.resume();
    playTone(300, 'sawtooth', 0.2, 0.2);
    setTimeout(() => playTone(250, 'sawtooth', 0.4, 0.2), 150);
};

const playClickSound = () => {
    if(audioCtx.state === 'suspended') audioCtx.resume();
    playTone(800, 'sine', 0.05, 0.05);
};

</script>
@endpush


