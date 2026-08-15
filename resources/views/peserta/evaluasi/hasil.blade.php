@extends('layouts.app')

@section('title', 'Hasil Evaluasi')

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('peserta.evaluasi.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Menu Evaluasi
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Score Summary Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8 text-center sticky top-24">
            <h2 class="text-lg font-bold text-text-secondary mb-2">Nilai Akhir Anda</h2>
            <div class="w-32 h-32 mx-auto rounded-full border-8 flex items-center justify-center mb-4 {{ $sesi->skor >= 70 ? 'border-success text-success' : 'border-danger text-danger' }}">
                <span class="text-4xl font-display font-bold">{{ $sesi->skor }}</span>
            </div>
            
            @if($sesi->skor >= 70)
                <p class="text-success font-bold text-lg mb-1">Lulus!</p>
                <p class="text-sm text-text-secondary">Selamat, Anda telah memahami materi dengan baik.</p>
            @else
                <p class="text-danger font-bold text-lg mb-1">Belum Lulus</p>
                <p class="text-sm text-text-secondary">Silakan pelajari kembali modul Anda dan ulangi kuis jika diperlukan.</p>
            @endif
            
            <div class="mt-6 pt-6 border-t border-border grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-text-secondary font-medium">Jawaban Benar</p>
                    <p class="text-xl font-bold text-text-primary">{{ $sesi->benar }} / {{ $sesi->total_soal }}</p>
                </div>
                <div>
                    <p class="text-xs text-text-secondary font-medium">Waktu Selesai</p>
                    <p class="text-sm font-bold text-text-primary">{{ $sesi->selesai_at->format('d M, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Answers Review -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
            <div class="p-4 border-b border-border bg-secondary/50">
                <h3 class="font-display font-bold text-text-primary">Pembahasan Jawaban</h3>
            </div>
            
            <div class="p-6 space-y-6">
                @foreach($hasilLatihans as $index => $hasil)
                    <div class="pb-6 border-b border-border last:border-0 last:pb-0">
                        <div class="flex gap-4">
                            <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm text-white {{ $hasil->is_correct ? 'bg-success' : 'bg-danger' }}">
                                @if($hasil->is_correct)
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                @else
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-text-primary font-medium mb-3">
                                    <span class="font-bold mr-1">{{ $index + 1 }}.</span>
                                    {{ $hasil->soal->pertanyaan }}
                                </p>

                                <div class="space-y-2 mb-4">
                                    @foreach($hasil->soal->pilihanJawaban as $pilihan)
                                        @php
                                            $isUserChoice = $hasil->pilihan_id == $pilihan->id;
                                            $isCorrectChoice = $pilihan->is_correct;
                                            
                                            $bgClass = 'bg-secondary/50 border-transparent text-text-secondary';
                                            if ($isCorrectChoice) {
                                                $bgClass = 'bg-success/10 border-success text-success font-medium';
                                            } elseif ($isUserChoice && !$isCorrectChoice) {
                                                $bgClass = 'bg-danger/10 border-danger text-danger font-medium';
                                            }
                                        @endphp
                                        <div class="flex items-start gap-2 p-2 rounded border {{ $bgClass }}">
                                            <span class="font-bold w-6">{{ $pilihan->huruf }}.</span>
                                            <span>{{ $pilihan->teks }}</span>
                                            
                                            @if($isUserChoice)
                                                <span class="ml-auto text-xs font-bold border rounded px-1 {{ $isCorrectChoice ? 'border-success text-success' : 'border-danger text-danger' }}">Jawaban Anda</span>
                                            @endif
                                            @if($isCorrectChoice && !$isUserChoice)
                                                <span class="ml-auto text-xs font-bold border border-success text-success rounded px-1">Kunci</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if($hasil->soal->pembahasan)
                                    <div class="bg-primary/5 border border-primary/20 rounded-lg p-3 text-sm">
                                        <p class="font-bold text-primary mb-1 text-xs uppercase tracking-wider">Pembahasan:</p>
                                        <p class="text-text-secondary">{{ $hasil->soal->pembahasan }}</p>
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

@endsection
