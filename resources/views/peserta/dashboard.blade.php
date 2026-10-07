@extends('layouts.app')

@section('title', 'Dashboard Pembelajaran - STRAPSUSPAS')

@section('content')
    <div class="space-y-6 max-w-7xl mx-auto">

        {{-- 1. CLEAN & WELCOMING HERO HEADER --}}
        <div
            class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 sm:p-7 shadow-xs relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative z-10">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                            Peserta Pembinaan ASN
                        </span>
                        <span class="text-xs text-[var(--text-secondary)]">
                            NIP: <strong
                                class="text-[var(--text-primary)] font-medium">{{ auth()->user()->nip ?? '-' }}</strong>
                            &bull; {{ auth()->user()->unitKerja->nama ?? 'Kanwil Ditjenpas Sulsel' }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-heading font-bold text-[var(--text-primary)] tracking-tight">
                        Selamat Datang, {{ auth()->user()->nama }}
                    </h1>
                    <p class="text-xs sm:text-sm text-[var(--text-secondary)] max-w-xl">
                        Ambil napas dalam-dalam, bangkit, bersihkan diri, dan mulai dari awal lagi...
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('peserta.pelatihan.index') }}"
                        class="btn btn-primary text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-xs transition-all inline-flex items-center gap-2">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                        <span>Katalog Pelatihan</span>
                    </a>
                    <a href="{{ route('peserta.statistik.index') }}"
                        class="btn btn-secondary text-xs font-medium py-2.5 px-4 rounded-xl border border-[var(--border)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-all inline-flex items-center gap-2">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-primary"></i>
                        <span>Statistik Nilai</span>
                    </a>
                </div>
            </div>

            {{-- Soft subtle background glow --}}
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        {{-- 2. RINGKASAN AKTIVITAS (3 COMPACT KPI CARDS) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Card 1: Modul Selesai --}}
            <div
                class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-4.5 sm:p-5 shadow-xs flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-medium text-[var(--text-secondary)]">Modul Selesai</span>
                    <div class="text-2xl sm:text-3xl font-heading font-bold text-[var(--text-primary)] tracking-tight">
                        {{ $materiSelesai }} <span class="text-xs font-normal text-[var(--text-muted)]">/ {{ $totalMateri }}
                            Modul</span>
                    </div>
                    <div class="text-[11px] text-[var(--text-muted)]">
                        {{ $totalMateri > 0 ? round(($materiSelesai / $totalMateri) * 100) : 0 }}% materi kurikulum tuntas
                    </div>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
            </div>

            {{-- Card 2: Tugas Menunggu --}}
            <div
                class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-4.5 sm:p-5 shadow-xs flex items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-medium text-[var(--text-secondary)]">Penugasan</span>
                    <div class="text-2xl sm:text-3xl font-heading font-bold text-[var(--text-primary)] tracking-tight">
                        {{ $pendingTugasPeserta->count() }} <span
                            class="text-xs font-normal text-[var(--text-muted)]">Menunggu</span>
                    </div>
                    <div class="text-[11px] text-[var(--text-muted)]">
                        {{ $pendingTugasPeserta->count() > 0 ? 'Perlu dikerjakan sebelum batas waktu' : 'Semua tugas telah diselesaikan' }}
                    </div>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                </div>
            </div>

            {{-- Card 3: Progres Belajar --}}
            <div
                class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-4.5 sm:p-5 shadow-xs flex items-center justify-between gap-4">
                <div class="space-y-1 flex-1 min-w-0">
                    <span class="text-xs font-medium text-[var(--text-secondary)]">Progres Keseluruhan</span>
                    <div class="text-2xl sm:text-3xl font-heading font-bold text-primary tracking-tight">
                        {{ $progres }}%
                    </div>
                    <div class="w-full bg-[var(--muted)] rounded-full h-1.5 overflow-hidden mt-1">
                        <div class="bg-primary h-full rounded-full transition-all duration-500"
                            style="width: {{ $progres }}%"></div>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        {{-- 3. KONTEN UTAMA (2 COLS: UTAMA & SAMPING) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- KOLOM UTAMA (2/3) --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- PELATIHAN SEDANG DIIKUTI --}}
                <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-xs p-6 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
                        <div class="flex items-center gap-2.5">
                            <div
                                class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <i data-lucide="play-circle" class="w-4 h-4"></i>
                            </div>
                            <h2 class="text-sm font-heading font-semibold text-[var(--text-primary)]">
                                Pelatihan Sedang Diikuti
                            </h2>
                        </div>

                        @if($activePelatihan)
                            <span
                                class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                Aktif
                            </span>
                        @endif
                    </div>

                    @if($activePelatihan)
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs font-semibold text-primary uppercase tracking-wide">
                                    {{ $activePelatihan->kategori ?? 'Hukum Disiplin Pegawai' }}
                                </span>
                                <h3 class="text-lg font-heading font-bold text-[var(--text-primary)] mt-1">
                                    {{ $activePelatihan->judul }}
                                </h3>
                                @if($activePelatihan->deskripsi)
                                    <p class="text-xs text-[var(--text-secondary)] line-clamp-2 mt-1 leading-relaxed">
                                        {{ $activePelatihan->deskripsi }}
                                    </p>
                                @endif
                            </div>

                            {{-- Progress bar & stats --}}
                            <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-[var(--text-secondary)]">Kemajuan Modul</span>
                                    <span class="font-bold text-[var(--text-primary)]">
                                        {{ $activePelatihanMateriSelesai }} dari {{ $activePelatihanMateriCount }} Modul
                                        ({{ $activePelatihanPersen }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-[var(--muted)] rounded-full h-2 overflow-hidden">
                                    <div class="bg-primary h-full rounded-full transition-all duration-500"
                                        style="width: {{ $activePelatihanPersen }}%"></div>
                                </div>
                            </div>

                            <div class="pt-1 flex items-center justify-between">
                                <div class="text-xs text-[var(--text-secondary)] flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="book" class="w-3.5 h-3.5 text-primary"></i>
                                        {{ $activePelatihanMateriCount }} Modul
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="clipboard" class="w-3.5 h-3.5 text-purple-600"></i>
                                        {{ $activePelatihan->tugas->count() }} Tugas
                                    </span>
                                </div>

                                <a href="{{ route('peserta.pelatihan.show', $activePelatihan->id) }}"
                                    class="btn btn-primary text-white text-xs font-semibold py-2.5 px-5 rounded-xl shadow-xs transition-all inline-flex items-center gap-2">
                                    <span>Lanjutkan Belajar</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 space-y-3">
                            <div
                                class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center mx-auto">
                                <i data-lucide="book-open" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-heading font-bold text-[var(--text-primary)]">Belum Memilih Pelatihan
                                </h3>
                                <p class="text-xs text-[var(--text-secondary)] max-w-sm mx-auto mt-0.5">
                                    Jelajahi katalog pelatihan disiplin ASN untuk memulai modul pertama Anda.
                                </p>
                            </div>
                            <div class="pt-1">
                                <a href="{{ route('peserta.pelatihan.index') }}"
                                    class="btn btn-primary text-white text-xs font-semibold py-2 px-4 rounded-xl shadow-xs inline-flex items-center gap-2">
                                    <span>Buka Katalog Pelatihan</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- PROMPT PRETEST DIAGNOSTIK (HANYA MUNCUL JIKA BELUM MENGERJAKAN) --}}
                @if($pretestResults->isEmpty())
                    <div
                        class="bg-gradient-to-r from-purple-500/10 via-indigo-500/5 to-transparent border border-purple-500/20 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-purple-500/15 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                <i data-lucide="target" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-heading font-bold text-[var(--text-primary)]">Asesmen Awal (Pretest)
                                    Belum Diikuti</h4>
                                <p class="text-xs text-[var(--text-secondary)]">Ukur pemahaman dasar disiplin ASN sebelum
                                    mempelajari seluruh modul.</p>
                            </div>
                        </div>
                        <a href="{{ route('peserta.pretest.take') }}"
                            class="btn btn-primary text-white text-xs font-semibold py-2 px-4 rounded-xl shadow-xs shrink-0 inline-flex items-center gap-1.5">
                            <span>Mulai Pretest</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                @endif

            </div>

            {{-- KOLOM SAMPING (1/3): TUGAS & MENU CEPAT --}}
            <div class="space-y-5">

                {{-- PENUGASAN KASUS (PENDING TASKS) --}}
                <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-xs p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--border)]">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <h3 class="text-sm font-heading font-semibold text-[var(--text-primary)]">
                                Penugasan Kasus
                            </h3>
                        </div>

                        @if($pendingTugasPeserta->isNotEmpty())
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600">
                                {{ $pendingTugasPeserta->count() }}
                            </span>
                        @endif
                    </div>

                    @if($pendingTugasPeserta->isEmpty())
                        <div class="text-center py-6 space-y-2">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto">
                                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                            </div>
                            <p class="text-xs font-semibold text-[var(--text-primary)]">Semua Tugas Selesai</p>
                            <p class="text-[11px] text-[var(--text-secondary)]">
                                Tidak ada penugasan kasus yang menunggu saat ini.
                            </p>
                        </div>
                    @else
                        <div class="space-y-2.5">
                            @foreach($pendingTugasPeserta as $t)
                                <div
                                    class="p-3 rounded-xl border border-[var(--border)] bg-[var(--card)] hover:border-primary/40 transition-all space-y-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span
                                            class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $t->tipe === 'upload_sertifikat' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600' }}">
                                            {{ $t->tipe_label }}
                                        </span>
                                        @if($t->deadline)
                                            <span class="text-[10px] font-semibold text-rose-600 dark:text-rose-400">
                                                {{ $t->deadline->format('d M, H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="text-xs font-semibold text-[var(--text-primary)] line-clamp-1">
                                        {{ $t->judul }}
                                    </h4>
                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-[11px] text-[var(--text-muted)] truncate max-w-[130px]">
                                            {{ $t->pelatihan->judul ?? 'Modul' }}
                                        </span>
                                        <a href="{{ route('peserta.tugas.show', $t->id) }}"
                                            class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                                            <span>Kerjakan</span>
                                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- MENU CEPAT --}}
                <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-xs p-5 space-y-3">
                    <h3 class="text-xs font-heading font-semibold text-[var(--text-muted)] uppercase tracking-wider">
                        Akses Cepat
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('peserta.statistik.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-xl hover:bg-[var(--muted)]/40 transition-colors group text-xs text-[var(--text-secondary)] hover:text-[var(--text-primary)]">
                            <span class="flex items-center gap-2.5 font-medium">
                                <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
                                Statistik & Rekap Nilai
                            </span>
                            <i data-lucide="chevron-right"
                                class="w-3.5 h-3.5 text-[var(--text-muted)] group-hover:text-[var(--text-primary)] group-hover:translate-x-0.5 transition-all"></i>
                        </a>

                        <a href="{{ route('peserta.pelatihan.index') }}"
                            class="flex items-center justify-between p-2.5 rounded-xl hover:bg-[var(--muted)]/40 transition-colors group text-xs text-[var(--text-secondary)] hover:text-[var(--text-primary)]">
                            <span class="flex items-center gap-2.5 font-medium">
                                <i data-lucide="folder-open" class="w-4 h-4 text-blue-600"></i>
                                Katalog Pelatihan
                            </span>
                            <i data-lucide="chevron-right"
                                class="w-3.5 h-3.5 text-[var(--text-muted)] group-hover:text-[var(--text-primary)] group-hover:translate-x-0.5 transition-all"></i>
                        </a>

                        @if(!Auth::user()->hasActiveSesiEvaluasi())
                            <a href="{{ route('ai.index') }}"
                                class="flex items-center justify-between p-2.5 rounded-xl hover:bg-[var(--muted)]/40 transition-colors group text-xs text-[var(--text-secondary)] hover:text-[var(--text-primary)]">
                                <span class="flex items-center gap-2.5 font-medium">
                                    <i data-lucide="bot" class="w-4 h-4 text-purple-600"></i>
                                    Asisten AI Tutor Disiplin
                                </span>
                                <i data-lucide="chevron-right"
                                    class="w-3.5 h-3.5 text-[var(--text-muted)] group-hover:text-[var(--text-primary)] group-hover:translate-x-0.5 transition-all"></i>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection