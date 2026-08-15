@extends('layouts.evaluasi')

@section('content')

<div class="relative max-w-7xl mx-auto pb-20">
    <div class="flex items-start gap-4 md:gap-6">
        
        {{-- ========================================== --}}
        {{-- KOLOM KIRI: AREA SOAL (WIZARD)               --}}
        {{-- ========================================== --}}
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden relative">
                
                {{-- Header Area Soal --}}
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-border bg-secondary/20 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-text-primary">{{ $materi->judul }}</h2>
                        <p class="text-xs md:text-sm text-text-secondary mt-0.5">Jawablah pertanyaan berikut dengan cermat.</p>
                    </div>
                </div>

                <form id="quiz-form" action="{{ route('peserta.evaluasi.submit', $sesi->id) }}" method="POST" onsubmit="clearSavedAnswers()">
                    @csrf
                    
                    <div class="p-4 md:p-8">
                        @foreach($soals as $index => $soal)
                            <div class="soal-item {{ $index === 0 ? '' : 'hidden' }}" id="soal-step-{{ $index + 1 }}" data-index="{{ $index + 1 }}">
                                <div class="flex gap-3 md:gap-4 mb-4 md:mb-6">
                                    <div class="shrink-0 w-8 h-8 md:w-10 md:h-10 bg-primary/10 text-primary rounded-lg md:rounded-xl flex items-center justify-center font-bold md:text-lg border border-primary/20">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 pt-1 md:pt-1.5">
                                        {{-- Teks Soal --}}
                                        <div class="text-text-primary font-medium text-base md:text-lg leading-relaxed mb-4 md:mb-6">
                                            {!! nl2br(e($soal->pertanyaan)) !!}
                                        </div>

                                        {{-- Pilihan Jawaban / Input --}}
                                        <div class="space-y-3">
                                            @if($soal->tipe === 'pilihan_ganda')
                                                @foreach($soal->pilihanJawaban as $pilihan)
                                                    <label class="flex items-start gap-3 md:gap-4 p-3 md:p-4 rounded-xl border border-border hover:border-primary hover:bg-primary/5 cursor-pointer transition-all group">
                                                        <div class="flex items-center h-5 mt-0.5">
                                                            <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $pilihan->id }}" 
                                                                onchange="markAnswered({{ $index + 1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                                class="w-5 h-5 text-primary focus:ring-primary border-border">
                                                        </div>
                                                        <div class="flex-1 text-sm md:text-base">
                                                            @if($pilihan->label)<span class="font-bold text-text-primary mr-2">{{ $pilihan->label }}.</span>@endif
                                                            <span class="text-text-secondary group-hover:text-text-primary transition-colors">{{ $pilihan->teks }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach

                                            @elseif($soal->tipe === 'multi_select')
                                                @foreach($soal->pilihanJawaban as $pilihan)
                                                    <label class="flex items-start gap-3 md:gap-4 p-3 md:p-4 rounded-xl border border-border hover:border-primary hover:bg-primary/5 cursor-pointer transition-all group">
                                                        <div class="flex items-center h-5 mt-0.5">
                                                            <input type="checkbox" name="jawaban[{{ $soal->id }}][]" value="{{ $pilihan->id }}" 
                                                                onchange="markAnswered({{ $index + 1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                                class="w-5 h-5 text-primary rounded border-border focus:ring-primary">
                                                        </div>
                                                        <div class="flex-1 text-sm md:text-base">
                                                            @if($pilihan->label)<span class="font-bold text-text-primary mr-2">{{ $pilihan->label }}.</span>@endif
                                                            <span class="text-text-secondary group-hover:text-text-primary transition-colors">{{ $pilihan->teks }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach

                                            @elseif($soal->tipe === 'essay' || $soal->tipe === 'isian_singkat')
                                                <textarea name="jawaban[{{ $soal->id }}]" rows="{{ $soal->tipe === 'essay' ? 5 : 2 }}" 
                                                    oninput="markAnswered({{ $index + 1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                    placeholder="Ketik jawaban Anda di sini..."
                                                    class="w-full px-4 md:px-5 py-3 md:py-4 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-y text-sm md:text-base"></textarea>

                                            @elseif($soal->tipe === 'menjodohkan')
                                                <div class="grid grid-cols-1 gap-3">
                                                    @foreach($soal->pilihanJawaban as $pilihan)
                                                        @php $parts = explode('|||', $pilihan->teks); @endphp
                                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                                            <div class="w-full sm:flex-1 p-3 bg-secondary/50 rounded-lg text-sm font-medium border border-border">
                                                                {{ $parts[0] ?? '' }}
                                                            </div>
                                                            <i data-lucide="arrow-down" class="w-4 h-4 text-text-secondary shrink-0 hidden sm:block sm:rotate-[-90deg]"></i>
                                                            <div class="w-full sm:flex-1">
                                                                <input type="text" name="jawaban[{{ $soal->id }}][{{ $pilihan->id }}]" 
                                                                    oninput="markAnswered({{ $index + 1 }}, {{ $soal->id }}, '{{ $soal->tipe }}')"
                                                                    placeholder="Ketik jodohnya..." 
                                                                    class="w-full px-3 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary outline-none text-sm">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer Navigasi Prev/Next --}}
                    <div class="px-4 py-3 md:px-6 md:py-5 border-t border-border bg-secondary/20 flex items-center justify-between">
                        <button type="button" id="btn-prev" onclick="prevSoal()" class="hidden px-4 md:px-5 py-2 md:py-2.5 rounded-lg border border-border bg-white text-text-secondary hover:text-primary hover:border-primary font-bold text-xs md:text-sm transition-all flex items-center gap-1 md:gap-2">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i> Sebelumnya
                        </button>
                        <div class="flex-1"></div>
                        <button type="button" id="btn-next" onclick="nextSoal()" class="px-4 md:px-5 py-2 md:py-2.5 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold text-xs md:text-sm transition-all shadow-sm flex items-center gap-1 md:gap-2">
                            Selanjutnya <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- KOLOM KANAN: QUESTION MAP                  --}}
        {{-- ========================================== --}}
        <div class="w-[200px] md:w-[260px] shrink-0">
            <div class="sticky top-20 space-y-4">

                {{-- Question Map --}}
                <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
                    <div class="px-4 py-3 border-b border-border bg-secondary/20">
                        <h3 class="font-bold text-text-primary flex items-center gap-2 text-sm">
                            <i data-lucide="layout-grid" class="w-4 h-4 text-primary"></i> Navigasi Soal
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-wrap gap-1.5" id="question-map">
                            @foreach($soals as $index => $soal)
                                <button type="button" onclick="goToSoal({{ $index + 1 }})" id="map-btn-{{ $index + 1 }}"
                                    class="relative w-7 h-9 border-2 border-text-secondary rounded-sm text-xs font-semibold flex items-start justify-center pt-0.5 overflow-hidden transition-all hover:border-primary bg-white text-text-primary">
                                    <div id="map-bg-{{ $index + 1 }}" class="absolute bottom-0 left-0 right-0 h-1/2 bg-text-secondary hidden"></div>
                                    <span class="relative z-10">{{ $index + 1 }}</span>
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-5 border-t border-border space-y-3">
                            <div class="flex items-center gap-3 text-xs text-text-secondary font-medium">
                                <div class="w-6 h-8 border-2 border-primary ring-2 ring-primary/30 rounded-sm bg-white"></div>
                                <span>Posisi Saat Ini</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-text-secondary font-medium">
                                <div class="w-6 h-8 border-2 border-text-secondary rounded-sm bg-white relative">
                                    <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-text-secondary"></div>
                                </div>
                                <span>Sudah Dijawab</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-text-secondary font-medium">
                                <div class="w-6 h-8 border-2 border-text-secondary rounded-sm bg-white"></div>
                                <span>Belum Dijawab</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="button" onclick="confirmSubmit()" class="w-full bg-accent hover:bg-accent-hover text-white font-bold py-4 px-6 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-lg">
                    <i data-lucide="check-square" class="w-6 h-6"></i> Kumpulkan
                </button>
                <p class="text-center text-xs text-text-secondary mt-2 px-4">
                    Pastikan semua soal telah terjawab.
                </p>

            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const totalSoal = {{ $soals->count() }};
    let currentSoal = 1;

    // Initialize View
    document.addEventListener('DOMContentLoaded', () => {
        updateView();
        
        // Timer Logic
        @if($materi->durasi_menit > 0)
            const sisaDetikAwal = {{ $sesi->sisaWaktu }};
            let distance = sisaDetikAwal * 1000;
            
            const timerInterval = setInterval(function() {
                distance -= 1000;

                if (distance <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById("countdown-timer").innerHTML = "00:00";
                    alert("Waktu Habis! Jawaban Anda akan otomatis dikumpulkan.");
                    document.getElementById("quiz-form").submit();
                    return;
                }

                const minutes = Math.floor(distance / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown-timer").innerHTML = 
                    (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                    (seconds < 10 ? "0" + seconds : seconds);
                    
                if (distance < 300000) { // under 5 minutes
                    document.getElementById("countdown-timer").classList.add('text-danger');
                    document.getElementById("countdown-timer").parentElement.classList.replace('text-warning', 'text-danger');
                    document.getElementById("countdown-timer").parentElement.classList.replace('bg-warning/10', 'bg-danger/10');
                    document.getElementById("countdown-timer").parentElement.classList.replace('border-warning/20', 'border-danger/20');
                }
            }, 1000);
        @endif
    });

    // Navigation Logic
    function nextSoal() {
        if (currentSoal < totalSoal) {
            currentSoal++;
            updateView();
        }
    }

    function prevSoal() {
        if (currentSoal > 1) {
            currentSoal--;
            updateView();
        }
    }

    function goToSoal(index) {
        currentSoal = index;
        updateView();
    }

    function updateView() {
        // Hide all, show current
        for (let i = 1; i <= totalSoal; i++) {
            document.getElementById(`soal-step-${i}`).classList.add('hidden');
        }
        document.getElementById(`soal-step-${currentSoal}`).classList.remove('hidden');

        // Button states
        document.getElementById('btn-prev').classList.toggle('hidden', currentSoal === 1);
        document.getElementById('btn-next').classList.toggle('hidden', currentSoal === totalSoal);

        // Update Question Map styling
        updateMapStyling();
    }

    const quizSesiId = {{ $sesi->id }};
    const storageKey = `quiz_answers_${quizSesiId}`;

    // Auto-save logic
    function saveAnswers() {
        const form = document.getElementById('quiz-form');
        const formData = new FormData(form);
        const data = {};
        for(let [key, value] of formData.entries()) {
            if (key !== '_token') {
                if(!data[key]) {
                    data[key] = [];
                }
                data[key].push(value);
            }
        }
        localStorage.setItem(storageKey, JSON.stringify(data));
    }

    function loadAnswers() {
        const saved = localStorage.getItem(storageKey);
        if(!saved) return;
        try {
            const data = JSON.parse(saved);
            const form = document.getElementById('quiz-form');
            
            for(let key in data) {
                const values = data[key];
                const inputs = form.querySelectorAll(`[name="${key}"], [name="${key}[]"]`);
                if(inputs.length === 0) continue;
                
                inputs.forEach(input => {
                    if(input.type === 'radio' || input.type === 'checkbox') {
                        if(values.includes(input.value)) {
                            input.checked = true;
                        }
                    } else {
                        input.value = values[0] || '';
                    }
                });
            }
            // Restore visual indicators
            updateAnsweredStatus();
        } catch(e) {
            console.error("Failed to restore answers", e);
        }
    }

    function clearSavedAnswers() {
        localStorage.removeItem(storageKey);
    }

    function markAnswered(index, soalId, tipe) {
        checkQuestionAnswered(index, soalId, tipe);
        saveAnswers();
    }

    function updateAnsweredStatus() {
        @foreach($soals as $index => $soal)
            checkQuestionAnswered({{ $index + 1 }}, {{ $soal->id }}, '{{ $soal->tipe }}');
        @endforeach
    }

    function checkQuestionAnswered(index, soalId, tipe) {
        let isAnswered = false;
        const form = document.getElementById('quiz-form');
        if (tipe === 'pilihan_ganda' || tipe === 'multi_select') {
            const checked = form.querySelector(`input[name^="jawaban[${soalId}]"]:checked`);
            isAnswered = !!checked;
        } else if (tipe === 'essay' || tipe === 'isian_singkat') {
            const val = form.querySelector(`textarea[name="jawaban[${soalId}]"]`).value.trim();
            isAnswered = val.length > 0;
        } else if (tipe === 'menjodohkan') {
            const inputs = form.querySelectorAll(`input[name^="jawaban[${soalId}]"]`);
            inputs.forEach(input => {
                if (input.value.trim().length > 0) isAnswered = true;
            });
        }
        
        const btn = document.getElementById(`map-btn-${index}`);
        if(btn) {
            btn.dataset.answered = isAnswered ? 'true' : 'false';
        }
        updateMapStyling();
    }

    function updateMapStyling() {
        for (let i = 1; i <= totalSoal; i++) {
            const btn = document.getElementById(`map-btn-${i}`);
            const bg = document.getElementById(`map-bg-${i}`);
            const isAnswered = btn.dataset.answered === 'true';
            const isActive = (i === currentSoal);

            // Reset base classes
            btn.className = "relative w-7 h-9 border-2 rounded-sm text-xs font-semibold flex items-start justify-center pt-0.5 overflow-hidden transition-all bg-white";

            // Active vs Inactive
            if (isActive) {
                btn.classList.add('border-primary', 'ring-2', 'ring-primary/30', 'text-text-primary');
            } else {
                btn.classList.add('border-text-secondary', 'text-text-primary', 'hover:border-primary');
            }

            // Answered vs Unanswered
            if (isAnswered) {
                bg.classList.remove('hidden');
            } else {
                bg.classList.add('hidden');
            }
        }
    }

    // Submit Logic
    function confirmSubmit() {
        // Count unanswered
        let unanswered = 0;
        for (let i = 1; i <= totalSoal; i++) {
            if (document.getElementById(`map-btn-${i}`).dataset.answered !== 'true') {
                unanswered++;
            }
        }

        let msg = 'Apakah Anda yakin ingin mengumpulkan jawaban sekarang? Anda tidak dapat mengubahnya lagi setelah ini.';
        if (unanswered > 0) {
            msg = `PERINGATAN: Ada ${unanswered} soal yang belum dijawab!\n\n` + msg;
        }

        if (confirm(msg)) {
            document.getElementById('quiz-form').submitted = true;
            document.getElementById('quiz-form').submit();
        }
    }

    // Unload warning
    window.addEventListener('beforeunload', function (e) {
        if (!document.getElementById('quiz-form').submitted) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadAnswers();
        updateMapStyling();
    });
</script>
@endpush
