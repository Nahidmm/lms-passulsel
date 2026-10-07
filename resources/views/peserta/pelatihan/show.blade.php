@extends('layouts.app')
@section('title', $pelatihan->judul)

@section('content')
<div class="space-y-6 py-2">

    {{-- Breadcrumb / Back link --}}
    <div class="flex items-center gap-2 text-xs text-[var(--text-secondary)]">
        <a href="{{ route('peserta.pelatihan.index') }}" class="hover:text-indigo-600 transition-colors flex items-center gap-1.5 font-medium">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Katalog Pelatihan
        </a>
        <span>/</span>
        <span class="text-[var(--text-primary)] font-semibold truncate">{{ $pelatihan->judul }}</span>
    </div>

    {{-- Course Header Banner --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 md:p-8 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex-1 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        <i data-lucide="book-open" class="w-3.5 h-3.5"></i> Modul
                    </span>
                    @if($persenProgress >= 100)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <i data-lucide="check-circle" class="w-3 h-3"></i> Tuntas
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-[var(--text-primary)] leading-tight">
                    {{ $pelatihan->judul }}
                </h1>
                <p class="text-sm text-[var(--text-secondary)] leading-relaxed max-w-3xl">
                    {{ $pelatihan->deskripsi }}
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 shrink-0">
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)] text-center min-w-[100px]">
                    <p class="text-xs text-[var(--text-secondary)] font-medium">Total Materi</p>
                    <p class="text-xl font-extrabold text-[var(--text-primary)] mt-0.5">{{ $materis->count() }}</p>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)] text-center min-w-[100px]">
                    <p class="text-xs text-[var(--text-secondary)] font-medium">Durasi Belajar</p>
                    <p class="text-xl font-extrabold text-[var(--text-primary)] mt-0.5">{{ $materis->sum('durasi_baca') + $materis->sum('durasi_menit') }} <span class="text-xs font-normal">mnt</span></p>
                </div>
                <div class="p-3.5 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900/60 text-center col-span-2 sm:col-span-1 min-w-[100px]">
                    <p class="text-xs text-indigo-700 dark:text-indigo-300 font-semibold">Progres</p>
                    <p class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $persenProgress }}%</p>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="mt-6 pt-5 border-t border-[var(--border)]">
            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                <span class="text-[var(--text-secondary)]">Penyelesaian Kurikulum</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $persenProgress }}% Terpenuhi</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $persenProgress }}%"></div>
            </div>
        </div>
    </div>

    {{-- CERTIFICATE BANNER (Jika selesai) --}}
    @if($progresPelatihan && $progresPelatihan->status === 'selesai')
    <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-950/20 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 dark:text-emerald-300 mb-0.5">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Selamat! Kurikulum Telah Selesai
                </div>
                <h3 class="text-base font-bold text-[var(--text-primary)]">Sertifikat Kelulusan Siap Diunduh</h3>
                <p class="text-xs text-[var(--text-secondary)] mt-0.5">
                    Anda telah menyelesaikan seluruh modul dan evaluasi pada pelatihan <strong class="text-[var(--text-primary)]">{{ $pelatihan->judul }}</strong>.
                </p>
                @if($sertifikat)
                    <p class="text-[11px] text-[var(--text-muted)] mt-1.5 font-mono">
                        Nomor Registrasi: <span class="font-semibold text-emerald-700 dark:text-emerald-400">{{ $sertifikat->credential_id }}</span> &bull; Diterbitkan {{ $sertifikat->issued_at->translatedFormat('d F Y') }}
                    </p>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0 w-full md:w-auto">
            @if($sertifikat)
                <a href="{{ route('peserta.sertifikat.download', $sertifikat->credential_id) }}" target="_blank" class="btn btn-primary text-xs w-full sm:w-auto justify-center bg-emerald-600 hover:bg-emerald-700">
                    <i data-lucide="download" class="w-4 h-4"></i> Unduh Sertifikat (PDF)
                </a>
                <a href="{{ route('sertifikat.verify', $sertifikat->credential_id) }}" target="_blank" class="btn btn-secondary text-xs w-full sm:w-auto justify-center">
                    <i data-lucide="shield-check" class="w-4 h-4"></i> Cek Keaslian
                </a>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-semibold text-[var(--text-secondary)]">
                    <i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Sertifikat sedang diproses
                </span>
            @endif
        </div>
    </div>
    @endif

    {{-- Curriculum List --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
        <div class="p-5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="list-ordered" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="font-bold text-sm text-[var(--text-primary)]">Materi & Evaluasi Modul</h2>
                    <p class="text-xs text-[var(--text-secondary)]">Selesaikan materi secara bertahap sesuai alur pembelajaran</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[var(--text-secondary)]">
                {{ $materis->count() }} Modul
            </span>
        </div>

        @if($materis->isEmpty())
            <div class="py-14 text-center">
                <i data-lucide="inbox" class="w-10 h-10 text-slate-300 dark:text-slate-700 mx-auto mb-2"></i>
                <p class="text-xs text-[var(--text-muted)] font-medium">Belum ada materi pembelajaran yang ditambahkan.</p>
            </div>
        @else
            <div class="divide-y divide-[var(--border)]">
                @foreach($materis as $idx => $materi)
                    @php
                        $statusData   = $materiStatus[$materi->id] ?? null;
                        $isLocked     = $statusData ? $statusData['is_locked'] : false;
                        $statusMateri = $statusData ? $statusData['status']    : 'belum';
                    @endphp

                    @if($isLocked)
                    <div class="flex items-center justify-between p-4 md:p-5 opacity-60 bg-slate-50/30 dark:bg-slate-900/20 cursor-not-allowed">
                    @else
                    <a href="{{ route('peserta.pembelajaran.materi.show', $materi->id) }}" class="flex items-center justify-between p-4 md:p-5 hover:bg-[var(--card-hover)] transition-colors group">
                    @endif
                        <div class="flex items-center gap-4 min-w-0">
                            {{-- Number / Status Badge --}}
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 transition-colors
                                @if($statusMateri === 'selesai') bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300
                                @elseif($isLocked)               bg-slate-200 dark:bg-slate-800 text-slate-400
                                @else                            bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 @endif">
                                @if($statusMateri === 'selesai')
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                @elseif($isLocked)
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </div>

                            <div class="min-w-0">
                                <h3 class="font-semibold text-sm text-[var(--text-primary)] group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-1">
                                    {{ $materi->judul }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-[var(--text-secondary)]">
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        @if($materi->jenis === 'video_embed')
                                            <i data-lucide="video" class="w-3.5 h-3.5 text-blue-500"></i> Video
                                        @elseif($materi->jenis === 'link')
                                            <i data-lucide="external-link" class="w-3.5 h-3.5 text-sky-500"></i> Tautan
                                        @elseif($materi->jenis === 'quiz')
                                            <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-amber-500"></i> {{ $materi->is_posttest ? 'Post-test Akhir' : 'Kuis Evaluasi' }}
                                        @else
                                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-indigo-500"></i> Dokumen
                                        @endif
                                    </span>
                                    <span>&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        {{ $materi->jenis === 'quiz' ? ($materi->durasi_menit ?: 15) : $materi->durasi_baca }} menit
                                    </span>
                                    @if($materi->prasyarat_materi_id)
                                        <span>&bull;</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Bersyarat
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 ml-4">
                            @if($statusMateri === 'selesai')
                                <span class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Selesai
                                </span>
                            @elseif($isLocked)
                                <span class="text-xs text-slate-400 font-medium hidden sm:inline">Terkunci</span>
                            @else
                                <span class="hidden sm:inline-flex text-xs font-semibold text-indigo-600 dark:text-indigo-400 group-hover:underline">
                                    Mulai &rarr;
                                </span>
                            @endif
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                        </div>
                    @if($isLocked) </div> @else </a> @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Penugasan & Praktik Kasus HUKDIS --}}
    @if(isset($tugasList) && $tugasList->isNotEmpty())
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
        <div class="p-5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center">
                    <i data-lucide="file-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="font-bold text-sm text-[var(--text-primary)]">Penugasan & Studi Kasus</h2>
                    <p class="text-xs text-[var(--text-secondary)]">Unggah telaah kasus disiplin atau sertifikat eksternal yang diwajibkan</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                {{ $tugasList->count() }} Tugas
            </span>
        </div>

        <div class="divide-y divide-[var(--border)]">
            @foreach($tugasList as $tgs)
                @php
                    $sub = $userSubmissions[$tgs->id] ?? null;
                @endphp
                <a href="{{ route('peserta.tugas.show', $tgs->id) }}" class="flex items-center justify-between p-4 md:p-5 hover:bg-[var(--card-hover)] transition-colors group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-9 h-9 rounded-xl {{ $tgs->tipe === 'upload_sertifikat' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }} flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $tgs->tipe === 'upload_sertifikat' ? 'award' : 'file-text' }}" class="w-4 h-4"></i>
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $tgs->tipe === 'upload_sertifikat' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $tgs->tipe_label }}
                                </span>
                                @if($tgs->deadline)
                                    <span class="text-xs text-[var(--text-muted)]">&bull; Batas: {{ $tgs->deadline->format('d M, H:i') }}</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-sm text-[var(--text-primary)] group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                {{ $tgs->judul }}
                            </h3>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0 ml-4">
                        @if($sub)
                            @if($sub->status === 'graded')
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <i data-lucide="check" class="w-3 h-3"></i> Nilai: {{ number_format($sub->nilai, 1) }}
                                </span>
                            @elseif($sub->status === 'need_revision')
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                    Perlu Revisi
                                </span>
                            @else
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu Review
                                </span>
                            @endif
                        @else
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[var(--text-secondary)]">
                                Belum Mengumpulkan
                            </span>
                        @endif
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Ringkasan Rekapitulasi Nilai Peserta --}}
    @if(isset($rekapNilai))
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-5 shadow-xs">
        <h3 class="font-bold text-sm text-[var(--text-primary)] flex items-center gap-2 mb-4">
            <i data-lucide="award" class="w-4 h-4 text-indigo-600"></i> Rekapitulasi Nilai Anda Pada Pelatihan Ini
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                <span class="text-[11px] text-[var(--text-secondary)] font-semibold uppercase block">Pretest</span>
                <span class="text-lg font-bold text-[var(--text-primary)] mt-1 block">{{ $rekapNilai->nilai_pretest !== null ? number_format($rekapNilai->nilai_pretest, 1) : '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                <span class="text-[11px] text-[var(--text-secondary)] font-semibold uppercase block">Kuis Formatif</span>
                <span class="text-lg font-bold text-[var(--text-primary)] mt-1 block">{{ $rekapNilai->nilai_quiz !== null ? number_format($rekapNilai->nilai_quiz, 1) : '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                <span class="text-[11px] text-[var(--text-secondary)] font-semibold uppercase block">Penugasan</span>
                <span class="text-lg font-bold text-[var(--text-primary)] mt-1 block">{{ $rekapNilai->nilai_tugas !== null ? number_format($rekapNilai->nilai_tugas, 1) : '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-[var(--border)]">
                <span class="text-[11px] text-[var(--text-secondary)] font-semibold uppercase block">Posttest</span>
                <span class="text-lg font-bold text-[var(--text-primary)] mt-1 block">{{ $rekapNilai->nilai_posttest !== null ? number_format($rekapNilai->nilai_posttest, 1) : '-' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900">
                <span class="text-[11px] text-indigo-700 dark:text-indigo-300 font-bold uppercase block">Nilai Akhir</span>
                <div class="flex items-center justify-center gap-1.5 mt-1">
                    <span class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400">{{ $rekapNilai->nilai_akhir !== null ? number_format($rekapNilai->nilai_akhir, 1) : '-' }}</span>
                    @if($rekapNilai->predikat)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-indigo-600 text-white">{{ $rekapNilai->predikat }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
