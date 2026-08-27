@extends('layouts.app')
@section('title', 'Asesmen Awal (Pretest)')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-violet-500/20 text-violet-400 mb-4 border border-violet-500/30 shadow-[0_0_15px_rgba(139,92,246,0.3)]">
            <i data-lucide="brain" class="w-8 h-8"></i>
        </div>
        <h1 class="text-3xl font-black text-[var(--text-primary)] mb-2" style="font-family:'Fraunces',serif;">{{ $pretest->judul }}</h1>
        <p class="text-[var(--text-secondary)]">{{ $pretest->deskripsi ?? 'Mari kita ukur pemahaman awal Anda sebelum memulai pelatihan.' }}</p>
    </div>

    <div class="game-card p-8 border border-[var(--border)] relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-violet-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-cyan-600/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 text-center">
            <h3 class="text-xl font-bold text-[var(--text-primary)] mb-4">Instruksi Pretest</h3>
            <ul class="text-left text-[var(--text-primary)] space-y-3 mb-8 max-w-lg mx-auto bg-black/20 p-6 rounded-xl border border-[var(--border)]">
                <li class="flex items-start gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5"></i>
                    <span>Pretest ini <strong>wajib</strong> diselesaikan sebelum Anda dapat mengakses fitur lain di SPEKTRA.</span>
                </li>
                <li class="flex items-start gap-3">
                    <i data-lucide="clock" class="w-5 h-5 text-amber-400 shrink-0 mt-0.5"></i>
                    <span>Pretest ini hanya dapat dilakukan <strong>satu kali</strong>. Pastikan Anda siap.</span>
                </li>
                <li class="flex items-start gap-3">
                    <i data-lucide="target" class="w-5 h-5 text-cyan-400 shrink-0 mt-0.5"></i>
                    <span>Hasil pretest akan digunakan untuk memetakan pemahaman Anda dan memberikan <strong>rekomendasi pelatihan</strong> yang sesuai.</span>
                </li>
            </ul>

            <form action="{{ route('peserta.evaluasi.start') }}" method="POST">
                @csrf
                <input type="hidden" name="materi_id" value="{{ $pretest->id }}">
                <button type="submit" class="btn btn-primary text-lg px-8 py-3 shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:shadow-[0_0_30px_rgba(139,92,246,0.6)] group">
                    Mulai Pretest Sekarang
                    <i data-lucide="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
