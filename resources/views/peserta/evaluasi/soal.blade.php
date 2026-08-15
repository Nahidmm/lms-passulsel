@extends('layouts.evaluasi')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8 relative">
    
    <div class="mb-6 pb-4 border-b border-border flex justify-between items-center">
        <h2 class="text-xl font-display font-bold text-text-primary">Kuis Evaluasi Kompetensi</h2>
        <span class="text-sm text-text-secondary">Sesi ID: #{{ $sesi->id }}</span>
    </div>

    <form id="quiz-form" action="{{ route('peserta.evaluasi.submit', $sesi->id) }}" method="POST">
        @csrf
        
        <div class="space-y-10">
            @foreach($soals as $index => $soal)
                <div class="soal-item" id="soal-{{ $index + 1 }}">
                    <div class="flex gap-4">
                        <div class="shrink-0 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="text-text-primary font-medium text-lg leading-relaxed mb-4">
                                {{ $soal->pertanyaan }}
                            </p>

                            @if($soal->tipe === 'pilgan')
                                <div class="space-y-3">
                                    @foreach($soal->pilihanJawaban as $pilihan)
                                        <label class="flex items-start gap-3 p-3 rounded-lg border border-border hover:bg-secondary cursor-pointer transition-colors relative group">
                                            <div class="flex items-center h-6">
                                                <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $pilihan->id }}" class="w-4 h-4 text-primary focus:ring-primary border-border">
                                            </div>
                                            <div class="flex-1">
                                                <span class="font-bold text-text-primary mr-2">{{ $pilihan->huruf }}.</span>
                                                <span class="text-text-secondary group-hover:text-text-primary">{{ $pilihan->teks }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 pt-6 border-t border-border flex justify-end">
            <button type="button" onclick="confirmSubmit()" class="bg-primary hover:bg-primary-hover text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors text-lg flex items-center gap-2">
                <i data-lucide="check-square" class="w-5 h-5"></i> Selesai & Kumpulkan
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    // Timer Logic
    const endTime = new Date("{{ $sesi->mulai_at->addMinutes($sesi->durasi_menit)->toIso8601String() }}").getTime();
    
    const timerInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            clearInterval(timerInterval);
            document.getElementById("countdown-timer").innerHTML = "Waktu Habis!";
            document.getElementById("quiz-form").submit();
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown-timer").innerHTML = 
            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
            (seconds < 10 ? "0" + seconds : seconds);
            
        if (distance < 300000) { // under 5 minutes
            document.getElementById("countdown-timer").parentElement.classList.replace('text-warning', 'text-danger');
            document.getElementById("countdown-timer").parentElement.classList.replace('bg-warning/10', 'bg-danger/10');
            document.getElementById("countdown-timer").parentElement.classList.replace('border-warning/20', 'border-danger/20');
        }
    }, 1000);

    function confirmSubmit() {
        if (confirm('Apakah Anda yakin ingin mengumpulkan jawaban sekarang? Anda tidak dapat mengubahnya lagi setelah ini.')) {
            document.getElementById('quiz-form').submit();
        }
    }

    // Unload warning
    window.addEventListener('beforeunload', function (e) {
        // Prevent default behavior if form is not submitted
        if(!document.getElementById('quiz-form').submitted) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    document.getElementById('quiz-form').addEventListener('submit', function() {
        this.submitted = true;
    });
</script>
@endpush
