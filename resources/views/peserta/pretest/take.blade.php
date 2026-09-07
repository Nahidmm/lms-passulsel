@extends('layouts.app')
@section('title', 'Asesmen Awal (Pretest) - STRAPSUSPAS')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 mb-4 shadow-sm ring-4 ring-indigo-500/10">
            <i data-lucide="clipboard-check" class="w-7 h-7"></i>
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-[var(--text-primary)] mb-2">{{ $pretest->judul }}</h1>
        <p class="text-xs md:text-sm text-[var(--text-secondary)] max-w-lg mx-auto">
            {{ $pretest->deskripsi ?? 'Asesmen awal diagnostik untuk mengukur tingkat pemahaman dan kepatuhan terhadap regulasi disiplin ASN sebelum memulai pelatihan.' }}
        </p>
    </div>

    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 md:p-8 shadow-xs">
        <div class="space-y-6">
            <div>
                <h3 class="font-bold text-sm text-[var(--text-primary)] mb-3 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-indigo-600"></i> Petunjuk Asesmen Awal:
                </h3>
                <div class="rounded-xl border border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 p-4 space-y-3 text-xs text-[var(--text-secondary)]">
                    <div class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5"></i>
                        <span>Asesmen ini <strong>wajib</strong> diselesaikan untuk membuka akses penuh ke modul pembelajaran disiplin ASN.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="clock" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                        <span>Kerjakan dengan jujur dan teliti sesuai pengetahuan Anda saat ini tanpa bantuan referensi.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                        <span>Hasil asesmen digunakan pimpinan dan admin untuk memetakan indikator kelemahan kompetensi disiplin pada tiap unit kerja.</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('peserta.evaluasi.start') }}" method="POST" class="text-center pt-2">
                @csrf
                <input type="hidden" name="materi_id" value="{{ $pretest->id }}">
                <button type="submit" class="btn btn-primary text-sm px-8 py-3.5 shadow-md justify-center w-full sm:w-auto inline-flex items-center gap-2">
                    <span>Mulai Kerjakan Asesmen Awal</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
