@extends('layouts.app')

@section('title', 'Detail Hasil Pretest - ' . $user->nama)

@section('content')
<div class="space-y-6 py-2">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.gradebook.pretest') }}" class="text-text-secondary hover:text-primary transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    Detail Pretest
                </span>
            </div>
            <h1 class="text-2xl font-bold text-text-primary">{{ $user->nama }}</h1>
            <p class="text-xs text-text-secondary mt-1">
                NIP: {{ $user->nip }} &bull; {{ $user->unitKerja?->nama_unit ?? 'Unit Kerja: -' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($sesi)
            <a href="{{ route('admin.gradebook.pretest.detail.pdf', $user->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors">
                <i data-lucide="file-down" class="w-4 h-4"></i> Download PDF
            </a>
            @endif
        </div>
    </div>

    @if(!$sesi)
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-12 text-center shadow-xs">
            <i data-lucide="clock" class="w-12 h-12 text-amber-500 mx-auto mb-3"></i>
            <h2 class="text-lg font-bold text-text-primary">Belum Mengerjakan Pretest</h2>
            <p class="text-xs text-text-secondary mt-1">Peserta ini belum menyelesaikan pretest, sehingga belum ada hasil yang dapat ditampilkan.</p>
        </div>
    @else
        {{-- Ringkasan Skor --}}
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-5 shadow-xs">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                    <span class="text-[10px] uppercase font-bold text-text-secondary block">Nilai Pretest</span>
                    <span class="text-2xl font-extrabold {{ $sesi->skor >= ($pretest->passing_grade ?? 70) ? 'text-emerald-600' : 'text-rose-600' }} mt-1 block">
                        {{ number_format((float)$sesi->skor, 1) }}
                    </span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                    <span class="text-[10px] uppercase font-bold text-text-secondary block">Jawaban Benar</span>
                    <span class="text-2xl font-extrabold text-text-primary mt-1 block">
                        {{ $sesi->benar }}<span class="text-sm font-normal text-text-muted">/{{ $sesi->total_soal }}</span>
                    </span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                    <span class="text-[10px] uppercase font-bold text-text-secondary block">Selesai</span>
                    <span class="text-sm font-bold text-text-primary mt-2 block">{{ $sesi->selesai_at ? $sesi->selesai_at->format('d M Y, H:i') : '-' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                    <span class="text-[10px] uppercase font-bold text-text-secondary block">Status</span>
                    <span class="text-sm font-bold {{ $sesi->skor >= ($pretest->passing_grade ?? 70) ? 'text-emerald-600' : 'text-rose-600' }} mt-2 block">
                        {{ $sesi->skor >= ($pretest->passing_grade ?? 70) ? 'Lulus' : 'Belum Lulus' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Koreksi Jawaban per Soal --}}
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
            <div class="p-5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50">
                <h3 class="font-bold text-sm text-text-primary">Koreksi Jawaban</h3>
                <p class="text-xs text-text-secondary">Rincian jawaban peserta untuk setiap butir soal pretest.</p>
            </div>

            <div class="p-5 md:p-6 divide-y divide-[var(--border)] space-y-6">
                @foreach($hasilLatihans as $index => $hasil)
                    <div class="pt-6 first:pt-0">
                        <div class="flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 mt-0.5
                                {{ $hasil->is_correct ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                @if($hasil->is_correct)
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                @else
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <span class="text-xs font-bold text-text-secondary">Soal No. {{ $index + 1 }}</span>
                                    <span class="text-[10px] uppercase font-bold text-text-muted border border-[var(--border)] rounded px-1.5 py-0.5">
                                        {{ strtoupper(str_replace('_', ' ', $hasil->soal->tipe)) }}
                                    </span>
                                    @if($hasil->is_correct)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Benar</span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">Salah</span>
                                    @endif
                                </div>

                                <h4 class="text-sm font-semibold text-text-primary leading-relaxed mb-4">
                                    {{ $hasil->soal->pertanyaan }}
                                </h4>

                                @if(in_array($hasil->soal->tipe, ['pilihan_ganda', 'multi_select', 'jenis_lainnya']))
                                <div class="space-y-2 mb-4">
                                    @foreach($hasil->soal->pilihanJawaban as $pilihan)
                                        @php
                                            $isUserChoice    = $hasil->pilihan_id == $pilihan->id;
                                            $isCorrectChoice = $pilihan->is_correct;

                                            $rowStyle = 'bg-slate-50/50 dark:bg-slate-900/50 border-[var(--border)] text-text-secondary';
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
                                                        Jawaban Peserta
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
                                @elseif(in_array($hasil->soal->tipe, ['isian_singkat', 'essay']))
                                <div class="p-3 rounded-xl border border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 mb-4">
                                    <p class="text-[11px] font-bold text-text-secondary mb-1">Jawaban Peserta:</p>
                                    <p class="text-sm text-text-primary">{{ $hasil->jawaban_esai ?: '(Tidak menjawab)' }}</p>
                                </div>
                                @endif

                                @if($hasil->catatan)
                                <div class="rounded-xl border border-indigo-200 dark:border-indigo-900 bg-indigo-50/50 dark:bg-indigo-950/20 p-3.5">
                                    <p class="text-[11px] font-bold text-indigo-700 dark:text-indigo-300 mb-1 flex items-center gap-1.5 uppercase tracking-wider">
                                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i> Catatan / Alasan Peserta
                                    </p>
                                    <p class="text-xs text-text-secondary leading-relaxed">{{ $hasil->catatan }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
