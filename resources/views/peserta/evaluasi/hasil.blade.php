@extends('layouts.app')
@section('title', 'Hasil Evaluasi')

@section('content')
<div class="space-y-5 py-1">

    {{-- Back --}}
    @if($materi->pelatihan_id)
        <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pelatihan
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
        </a>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- â”€â”€â”€â”€ Score Card â”€â”€â”€â”€ --}}
        <div class="lg:col-span-1">
            <div class="game-card p-6 md:p-8 text-center sticky top-20">

                @if($materi->mode_tampilan === 'interaktif')
                    <span class="badge badge-violet mb-4">Mode Interaktif</span>
                @endif

                <p class="text-sm font-semibold text-[var(--text-secondary)] mb-4">Nilai Akhirmu</p>

                {{-- Score circle --}}
                <div class="relative w-32 h-32 mx-auto mb-5">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="52" fill="none"
                            stroke="{{ $sesi->skor >= ($materi->passing_grade ?? 70) ? '#10b981' : '#f43f5e' }}"
                            stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ 2 * pi() * 52 }}"
                            stroke-dashoffset="{{ 2 * pi() * 52 * (1 - $sesi->skor / 100) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-4xl font-black {{ $sesi->skor >= ($materi->passing_grade ?? 70) ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ number_format((float)$sesi->skor, 0) }}
                        </span>
                    </div>
                </div>

                @if($sesi->skor >= ($materi->passing_grade ?? 70))
                    <p class="text-emerald-400 font-black text-xl mb-1">ðŸŽ‰ Lulus!</p>
                    <p class="text-sm text-[var(--text-secondary)]">Selamat! Kamu memahami materi ini dengan baik.</p>
                @else
                    <p class="text-rose-400 font-black text-xl mb-1">Belum Lulus</p>
                    <p class="text-sm text-[var(--text-secondary)]">Pelajari kembali materi dan coba lagi. Kamu pasti bisa!</p>
                @endif

                <div class="mt-6 pt-5 border-t border-[var(--border)] grid grid-cols-2 gap-4">
                    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl p-3">
                        <p class="text-xs text-[var(--text-muted)] font-semibold mb-1">Benar</p>
                        <p class="text-lg font-black text-[var(--text-primary)]">{{ $sesi->benar }}<span class="text-sm font-semibold text-[var(--text-muted)]">/{{ $sesi->total_soal }}</span></p>
                    </div>
                    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl p-3">
                        <p class="text-xs text-[var(--text-muted)] font-semibold mb-1">Selesai</p>
                        <p class="text-sm font-bold text-[var(--text-primary)]">{{ $sesi->selesai_at->format('d M, H:i') }}</p>
                    </div>
                </div>

                @if($materi->mode_tampilan === 'interaktif')
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="bg-violet-500/12 border border-violet-500/20 rounded-xl p-3 text-left relative group">
                            <p class="text-[9px] text-violet-400 uppercase tracking-widest font-bold flex items-center gap-1">
                                XP 
                                <span class="badge badge-emerald text-[8px] px-1 py-0.5" title="Bonus +20% Mode Interaktif">+20%</span>
                            </p>
                            <p class="text-xl font-black text-[var(--text-primary)] mt-1">{{ $sesi->xp_earned ?? 0 }}</p>
                        </div>
                        <div class="bg-amber-500/12 border border-amber-500/20 rounded-xl p-3 text-left">
                            <p class="text-[9px] text-amber-400 uppercase tracking-widest font-bold">Streak</p>
                            <p class="text-xl font-black text-[var(--text-primary)] mt-1">{{ $sesi->streak ?? 0 }}</p>
                        </div>
                    </div>
                @endif

                @if($materi->pelatihan_id)
                    <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="btn btn-primary w-full justify-center mt-5">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pelatihan
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary w-full justify-center mt-5">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
                    </a>
                @endif
            </div>
        </div>

        {{-- â”€â”€â”€â”€ Answer Review â”€â”€â”€â”€ --}}
        <div class="lg:col-span-2">
            <div class="game-card overflow-hidden">
                <div class="px-5 py-4 border-b border-[var(--border)] bg-[var(--card)] border border-[var(--border)] shadow-sm">
                    <h3 class="font-bold text-[var(--text-primary)] text-base">Pembahasan Jawaban</h3>
                </div>

                <div class="p-5 space-y-6">
                    @foreach($hasilLatihans as $index => $hasil)
                        <div class="pb-6 border-b border-[var(--border)] last:border-0 last:pb-0">
                            <div class="flex gap-4">

                                {{-- Correct/wrong indicator --}}
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm text-[var(--text-primary)] shrink-0
                                    {{ $hasil->is_correct ? 'bg-emerald-500/20 border border-emerald-500/40 text-emerald-400' : 'bg-rose-500/20 border border-rose-500/40 text-rose-400' }}">
                                    @if($hasil->is_correct)
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-[var(--text-primary)] font-semibold mb-4 leading-snug">
                                        <span class="text-[var(--text-muted)] font-bold mr-1">{{ $index + 1 }}.</span>
                                        {{ $hasil->soal->pertanyaan }}
                                    </p>

                                    <div class="space-y-2 mb-4">
                                        @foreach($hasil->soal->pilihanJawaban as $pilihan)
                                            @php
                                                $isUserChoice    = $hasil->pilihan_id == $pilihan->id;
                                                $isCorrectChoice = $pilihan->is_correct;

                                                $rowClass = 'bg-[var(--card)] border border-[var(--border)] shadow-sm border border-[var(--border)] text-[var(--text-secondary)]';
                                                if ($isCorrectChoice)                         $rowClass = 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-300';
                                                elseif ($isUserChoice && !$isCorrectChoice)   $rowClass = 'bg-rose-500/10 border border-rose-500/30 text-rose-300';
                                            @endphp
                                            <div class="flex items-start gap-3 p-3 rounded-xl border {{ $rowClass }}">
                                                <span class="font-black w-5 shrink-0 text-sm">{{ $pilihan->huruf }}.</span>
                                                <span class="flex-1 text-sm leading-relaxed">{{ $pilihan->teks }}</span>
                                                @if($isUserChoice)
                                                    <span class="text-[9px] font-black border rounded-full px-2 py-0.5 shrink-0 {{ $isCorrectChoice ? 'border-emerald-500/50 text-emerald-400' : 'border-rose-500/50 text-rose-400' }}">
                                                        Jawabanmu
                                                    </span>
                                                @endif
                                                @if($isCorrectChoice && !$isUserChoice)
                                                    <span class="text-[9px] font-black border border-emerald-500/50 text-emerald-400 rounded-full px-2 py-0.5 shrink-0">
                                                        Kunci
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($hasil->soal->pembahasan)
                                        <div class="bg-[#f0b429]/8 border border-blue-500/20 rounded-xl p-4">
                                            <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                                <i data-lucide="lightbulb" class="w-3 h-3"></i> Pembahasan
                                            </p>
                                            <p class="text-sm text-[var(--text-primary)] leading-relaxed">{{ $hasil->soal->pembahasan }}</p>
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

