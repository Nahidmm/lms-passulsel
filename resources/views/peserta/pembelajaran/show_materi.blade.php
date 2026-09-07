@extends('layouts.app')
@section('title', $materi->judul)

@section('content')
<div class="space-y-6 py-2">

    {{-- Breadcrumb Navigation --}}
    <div class="flex items-center gap-2 text-xs text-[var(--text-secondary)]">
        <a href="{{ route('peserta.pelatihan.index') }}" class="hover:text-indigo-600 transition-colors">Katalog Pelatihan</a>
        <span>/</span>
        <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">{{ $materi->pelatihan->judul ?? 'Pelatihan' }}</a>
        <span>/</span>
        <span class="text-[var(--text-primary)] font-semibold truncate">{{ $materi->judul }}</span>
    </div>

    {{-- Lesson Header Card --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 md:p-8 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        <i data-lucide="book" class="w-3.5 h-3.5"></i> Modul ke-{{ $materi->urutan }}
                    </span>

                    @if($progres->status === 'selesai')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <i data-lucide="check-circle" class="w-3 h-3"></i> Selesai Dipelajari
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            Sedang Berlangsung
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-[var(--text-primary)] leading-tight">
                    {{ $materi->judul }}
                </h1>

                <p class="text-sm text-[var(--text-secondary)] leading-relaxed max-w-3xl">
                    {{ $materi->deskripsi ?: 'Pelajari materi ini dengan saksama untuk memahami regulasi disiplin ASN dan implementasinya di lingkungan kerja.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)] text-center min-w-[110px]">
                    <span class="text-[11px] text-[var(--text-secondary)] font-medium block">Tipe Materi</span>
                    <span class="text-sm font-bold text-[var(--text-primary)] mt-0.5 block">
                        @if($materi->jenis === 'quiz')
                            {{ $materi->is_posttest ? 'Post-test Akhir' : 'Kuis Evaluasi' }}
                        @elseif($materi->jenis === 'video_embed')
                            Video Edukasi
                        @elseif($materi->jenis === 'link')
                            Tautan Eksternal
                        @else
                            Modul Dokumen
                        @endif
                    </span>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)] text-center min-w-[110px]">
                    <span class="text-[11px] text-[var(--text-secondary)] font-medium block">Estimasi Waktu</span>
                    <span class="text-sm font-bold text-[var(--text-primary)] mt-0.5 block">
                        {{ $materi->jenis === 'quiz' ? ($materi->durasi_menit ?: 15) : $materi->durasi_baca }} Menit
                    </span>
                </div>

                @if($materi->jenis === 'quiz')
                <div class="p-3.5 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900 text-center min-w-[110px]">
                    <span class="text-[11px] text-indigo-700 dark:text-indigo-300 font-semibold block">Batas Lulus</span>
                    <span class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5 block">
                        {{ $materi->passing_grade ?? 70 }} / 100
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Content Viewer Area --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-5 md:p-8 shadow-xs min-h-[350px]">
        @if($materi->jenis === 'link')
            <div class="flex flex-col items-center justify-center py-16 text-center max-w-md mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center mb-5 shadow-xs">
                    <i data-lucide="external-link" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-lg text-[var(--text-primary)] mb-2">Tautan Bahan Belajar Eksternal</h3>
                <p class="text-xs text-[var(--text-secondary)] mb-6 leading-relaxed">Materi ini merujuk ke sumber belajar atau portal regulasi eksternal. Silakan buka tautan di bawah untuk mempelajari lebih lanjut.</p>
                <a href="{{ $materi->url_link }}" target="_blank" class="btn btn-primary text-xs px-6 py-2.5">
                    <i data-lucide="external-link" class="w-4 h-4"></i> Buka Tautan Referensi
                </a>
            </div>

        @elseif($materi->jenis === 'video_embed')
            @php $embedUrl = str_replace('watch?v=', 'embed/', $materi->url_link); @endphp
            <div class="aspect-video w-full rounded-xl overflow-hidden border border-[var(--border)] bg-slate-950 shadow-sm">
                <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>

        @elseif($materi->jenis === 'quiz')
            <div class="flex flex-col items-center justify-center py-16 text-center max-w-lg mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center mb-5 shadow-xs">
                    <i data-lucide="clipboard-check" class="w-8 h-8"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[var(--text-primary)] mb-2">
                    {{ $materi->is_posttest ? 'Post-test Ujian Akhir' : 'Evaluasi Pemahaman Modul' }}
                </h3>
                <p class="text-xs text-[var(--text-secondary)] mb-6 leading-relaxed">
                    Uji pemahaman Anda terhadap substansi materi ini. Anda perlu mencapai nilai minimal <strong class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $materi->passing_grade ?? 70 }}</strong> untuk dinyatakan lulus modul ini.
                </p>
                <form action="{{ route('peserta.evaluasi.start') }}" method="POST">
                    @csrf
                    <input type="hidden" name="materi_id" value="{{ $materi->id }}">
                    <button type="submit" class="btn btn-primary text-xs px-8 py-3 shadow-md">
                        <i data-lucide="play" class="w-4 h-4"></i> Mulai Kerjakan Evaluasi
                    </button>
                </form>
            </div>

        @else
            @if($materi->file_path)
                <div class="w-full h-[680px] rounded-xl overflow-hidden border border-[var(--border)] bg-slate-100 dark:bg-slate-900">
                    <iframe src="{{ Storage::url($materi->file_path) }}" class="w-full h-full border-0"></iframe>
                </div>
            @else
                <div class="flex flex-col items-center py-20 text-center">
                    <i data-lucide="file-question" class="w-12 h-12 text-slate-300 dark:text-slate-700 mb-3"></i>
                    <p class="text-xs text-[var(--text-muted)] font-medium">Dokumen materi belum diunggah atau tidak ditemukan.</p>
                </div>
            @endif
        @endif
    </div>

    {{-- Bottom Actions / Completion Card --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4 md:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('peserta.pelatihan.show', $materi->pelatihan_id) }}" class="btn btn-secondary text-xs">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Kurikulum
            </a>
            <span class="text-xs text-[var(--text-secondary)] hidden sm:inline">
                Pastikan Anda telah menyimak seluruh materi sebelum melanjutkan.
            </span>
        </div>

        @if($progres->status !== 'selesai' && $materi->jenis !== 'quiz')
            <form action="{{ route('peserta.pembelajaran.materi.progress', $materi->id) }}" method="POST" class="shrink-0 w-full sm:w-auto">
                @csrf
                <button type="submit" class="btn btn-primary text-xs w-full sm:w-auto justify-center">
                    <i data-lucide="check" class="w-4 h-4"></i> Tandai Materi Selesai
                </button>
            </form>
        @elseif($progres->status === 'selesai')
            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-semibold text-xs px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Materi Telah Selesai
            </div>
        @endif
    </div>

</div>
@endsection
