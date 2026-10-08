@extends('layouts.app')

@section('title', 'Kelola Kursus: ' . $pelatihan->judul)

@section('content')
<div class="space-y-6">

    {{-- Top Header Banner --}}
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            {{-- Left Info --}}
            <div class="space-y-2">
                <div class="flex items-center flex-wrap gap-2.5">
                    <a href="{{ route('admin.pelatihan.index') }}" 
                       class="p-1.5 rounded-lg border border-[var(--border)] bg-[var(--background)] text-[var(--text-secondary)] hover:text-primary hover:border-primary/40 transition-all"
                       title="Kembali ke Daftar Pelatihan">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    </a>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pelatihan->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $pelatihan->is_active ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ $pelatihan->is_active ? 'Published / Aktif' : 'Draft' }}
                    </span>
                    <span class="text-xs text-[var(--text-muted)]">•</span>
                    <span class="text-xs font-semibold text-[var(--text-secondary)] uppercase tracking-wider">STRAPSUSPAS</span>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">{{ $pelatihan->judul }}</h1>
                @if($pelatihan->deskripsi)
                    <p class="text-xs text-[var(--text-secondary)] max-w-3xl leading-relaxed">{{ $pelatihan->deskripsi }}</p>
                @endif
            </div>

            {{-- Right Quick Actions --}}
            <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}" target="_blank"
                   class="btn btn-secondary text-xs font-medium py-2.5 px-3.5 rounded-xl border border-[var(--border)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-all inline-flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Pratinjau</span>
                </a>

                <a href="{{ route('admin.pelatihan.gradebook', $pelatihan->id) }}" 
                   class="btn btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border border-[var(--border)] text-[var(--text-primary)] hover:border-primary/40 transition-all inline-flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="award" class="w-3.5 h-3.5 text-primary"></i>
                    <span>Buku Nilai (Gradebook)</span>
                </a>

                <button type="button" onclick="openAddContentModal()" 
                   class="btn btn-primary text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-xs transition-all inline-flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Konten</span>
                </button>
            </div>
        </div>

        {{-- Summary Stats Pills --}}
        @php
            $materiCount = $pelatihan->materis->where('jenis', '!=', 'quiz')->count();
            $quizCount = $pelatihan->materis->where('jenis', 'quiz')->count();
            $tugasCount = $pelatihan->tugas->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-5 mt-5 border-t border-[var(--border)]">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)]">
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-secondary)]">Materi & Modul</p>
                    <p class="text-sm font-bold text-[var(--text-primary)]">{{ $materiCount }} Item</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)]">
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-secondary)]">Kuis & Ujian</p>
                    <p class="text-sm font-bold text-[var(--text-primary)]">{{ $quizCount }} Sesi</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)]">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="file-check-2" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-secondary)]">Penugasan</p>
                    <p class="text-sm font-bold text-[var(--text-primary)]">{{ $tugasCount }} Tugas</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <i data-lucide="sliders" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--text-secondary)]">Passing Grade</p>
                        <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $bobot->passing_grade }} Poin</p>
                    </div>
                </div>
                <button type="button" onclick="openBobotModal()" class="text-xs font-semibold text-primary hover:underline" title="Ubah Pengaturan Bobot">
                    Ubah
                </button>
            </div>
        </div>
    </div>

    {{-- Feedback Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 flex items-center gap-3 text-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 flex items-center gap-3 text-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Segmented Tabs Bar --}}
    <div class="flex items-center justify-between flex-wrap gap-3 border-b border-[var(--border)] pb-2">
        <div class="flex items-center gap-1.5 overflow-x-auto py-1" id="tabContainer">
            <button type="button" onclick="switchTab('semua')" id="tabBtn-semua"
                class="tab-btn bg-primary text-white shadow-xs px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                <span>Semua Aktivitas</span>
                <span class="tab-badge bg-white/20 text-white px-1.5 py-0.2 rounded-md text-[10px] font-bold">
                    {{ $materiCount + $quizCount + $tugasCount }}
                </span>
            </button>

            <button type="button" onclick="switchTab('materi')" id="tabBtn-materi"
                class="tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                <span>Materi & Modul</span>
                <span class="tab-badge bg-[var(--muted)] text-[var(--text-secondary)] px-1.5 py-0.2 rounded-md text-[10px] font-bold">
                    {{ $materiCount }}
                </span>
            </button>

            <button type="button" onclick="switchTab('kuis')" id="tabBtn-kuis"
                class="tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                <span>Kuis & Ujian</span>
                <span class="tab-badge bg-[var(--muted)] text-[var(--text-secondary)] px-1.5 py-0.2 rounded-md text-[10px] font-bold">
                    {{ $quizCount }}
                </span>
            </button>

            <button type="button" onclick="switchTab('tugas')" id="tabBtn-tugas"
                class="tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i>
                <span>Penugasan</span>
                <span class="tab-badge bg-[var(--muted)] text-[var(--text-secondary)] px-1.5 py-0.2 rounded-md text-[10px] font-bold">
                    {{ $tugasCount }}
                </span>
            </button>

            <button type="button" onclick="switchTab('bobot')" id="tabBtn-bobot"
                class="tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                <span>Bobot Kelulusan</span>
            </button>
        </div>

        {{-- Right Tab Action Buttons (Shown when specific tab active) --}}
        <div class="flex items-center gap-2">
            <div id="tabAction-materi" class="hidden">
                <a href="{{ route('admin.pelatihan.materi.create', $pelatihan->id) }}" 
                   class="btn btn-secondary text-xs font-semibold py-1.5 px-3 rounded-xl border border-[var(--border)] text-primary hover:bg-primary/5 inline-flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Materi
                </a>
            </div>
            <div id="tabAction-kuis" class="hidden">
                <a href="{{ route('admin.pelatihan.materi.create', ['pelatihan' => $pelatihan->id, 'jenis' => 'quiz']) }}" 
                   class="btn btn-secondary text-xs font-semibold py-1.5 px-3 rounded-xl border border-[var(--border)] text-purple-600 hover:bg-purple-500/5 inline-flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Kuis / Ujian
                </a>
            </div>
            <div id="tabAction-tugas" class="hidden">
                <a href="{{ route('admin.pelatihan.tugas.create', $pelatihan->id) }}" 
                   class="btn btn-secondary text-xs font-semibold py-1.5 px-3 rounded-xl border border-[var(--border)] text-amber-600 hover:bg-amber-500/5 inline-flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Penugasan
                </a>
            </div>
        </div>
    </div>

    {{-- ACTIVITY LISTS DATA --}}
    @php
        $materiItems = $pelatihan->materis->where('jenis', '!=', 'quiz');
        $quizItems = $pelatihan->materis->where('jenis', 'quiz');
        $tugasItems = $pelatihan->tugas;
    @endphp

    {{-- SECTION 1: MATERI & MODUL --}}
    <div id="section-materi" class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[var(--text-primary)] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="book-open" class="w-4 h-4 text-blue-500"></i>
                <span>Materi & Modul Pembelajaran</span>
                <span class="text-xs text-[var(--text-secondary)] font-normal">({{ $materiItems->count() }} modul)</span>
            </h3>
            <a href="{{ route('admin.pelatihan.materi.create', $pelatihan->id) }}" class="text-xs font-medium text-primary hover:underline inline-flex items-center gap-1">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
            </a>
        </div>

        @if($materiItems->isEmpty())
            <div class="bg-[var(--card)] border border-dashed border-[var(--border)] rounded-2xl p-8 text-center">
                <i data-lucide="folder-open" class="w-8 h-8 text-[var(--text-muted)] mx-auto mb-2 opacity-50"></i>
                <p class="text-xs text-[var(--text-secondary)] font-medium">Belum ada materi pembelajaran untuk pelatihan ini.</p>
                <a href="{{ route('admin.pelatihan.materi.create', $pelatihan->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary mt-2 hover:underline">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Materi Pertama
                </a>
            </div>
        @else
            <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl divide-y divide-[var(--border)] overflow-hidden shadow-xs">
                @foreach($materiItems as $materi)
                    <div class="p-4 flex items-center justify-between hover:bg-[var(--muted)]/30 transition-all group">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="w-7 h-7 rounded-xl bg-[var(--muted)] text-[var(--text-secondary)] flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $materi->urutan }}
                            </span>

                            @if($materi->jenis === 'video_embed')
                                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="youtube" class="w-4 h-4"></i>
                                </div>
                            @elseif($materi->jenis === 'link')
                                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="link" class="w-4 h-4"></i>
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </div>
                            @endif

                            <div class="min-w-0">
                                <h4 class="text-sm font-semibold text-[var(--text-primary)] group-hover:text-primary transition-colors truncate">
                                    {{ $materi->judul }}
                                </h4>
                                <p class="text-xs text-[var(--text-secondary)] mt-0.5 flex items-center gap-2">
                                    <span>{{ $materi->durasi_baca ?? 5 }} Menit</span>
                                    <span>•</span>
                                    <span class="uppercase font-medium text-[10px] tracking-wider">{{ str_replace('_', ' ', $materi->jenis) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('admin.materi.edit', $materi->id) }}" 
                               class="p-2 text-[var(--text-secondary)] hover:text-primary hover:bg-primary/5 rounded-xl border border-transparent hover:border-primary/20 transition-all" 
                               title="Edit Materi">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.materi.destroy', $materi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus materi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="p-2 text-[var(--text-secondary)] hover:text-danger hover:bg-danger/5 rounded-xl border border-transparent hover:border-danger/20 transition-all" 
                                    title="Hapus Materi">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- SECTION 2: KUIS & EVALUASI --}}
    <div id="section-kuis" class="space-y-4 pt-2">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[var(--text-primary)] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="help-circle" class="w-4 h-4 text-purple-500"></i>
                <span>Evaluasi & Ujian (Pretest, Kuis Formatif, Posttest)</span>
                <span class="text-xs text-[var(--text-secondary)] font-normal">({{ $quizItems->count() }} sesi)</span>
            </h3>
            <a href="{{ route('admin.pelatihan.materi.create', ['pelatihan' => $pelatihan->id, 'jenis' => 'quiz']) }}" class="text-xs font-medium text-purple-600 hover:underline inline-flex items-center gap-1">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Kuis
            </a>
        </div>

        @if($quizItems->isEmpty())
            <div class="bg-[var(--card)] border border-dashed border-[var(--border)] rounded-2xl p-8 text-center">
                <i data-lucide="help-circle" class="w-8 h-8 text-[var(--text-muted)] mx-auto mb-2 opacity-50"></i>
                <p class="text-xs text-[var(--text-secondary)] font-medium">Belum ada kuis evaluasi atau ujian untuk pelatihan ini.</p>
                <a href="{{ route('admin.pelatihan.materi.create', ['pelatihan' => $pelatihan->id, 'jenis' => 'quiz']) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-purple-600 mt-2 hover:underline">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Kuis Pertama
                </a>
            </div>
        @else
            <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl divide-y divide-[var(--border)] overflow-hidden shadow-xs">
                @foreach($quizItems as $quiz)
                    <div class="p-4 flex items-center justify-between hover:bg-[var(--muted)]/30 transition-all group">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="w-7 h-7 rounded-xl bg-[var(--muted)] text-[var(--text-secondary)] flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $quiz->urutan }}
                            </span>

                            <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                <i data-lucide="help-circle" class="w-4 h-4"></i>
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-semibold text-[var(--text-primary)] group-hover:text-purple-600 transition-colors truncate">
                                        {{ $quiz->judul }}
                                    </h4>
                                    @if($quiz->is_pretest)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">Pretest</span>
                                    @elseif($quiz->is_posttest)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Posttest / Ujian</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">Kuis Formatif</span>
                                    @endif
                                </div>
                                <p class="text-xs text-[var(--text-secondary)] mt-0.5 flex items-center gap-2">
                                    <span>{{ $quiz->durasi_menit ?? 'Bebas' }} Menit</span>
                                    <span>•</span>
                                    <span>Passing Grade: {{ $quiz->passing_grade ?? 70 }}%</span>
                                    <span>•</span>
                                    <span>{{ $quiz->soals->count() }} Butir Soal</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('admin.materi.soal.create', $quiz->id) }}" 
                               class="btn btn-secondary text-xs font-semibold py-1.5 px-3 rounded-xl border border-[var(--border)] text-primary hover:border-primary inline-flex items-center gap-1.5"
                               title="Tambah Butir Soal">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Soal
                            </a>
                            <a href="{{ route('admin.materi.edit', $quiz->id) }}" 
                               class="btn btn-secondary text-xs font-semibold py-1.5 px-3 rounded-xl border border-[var(--border)] text-purple-600 hover:border-purple-400 inline-flex items-center gap-1.5" 
                               title="Kelola Soal & Pengaturan Kuis">
                                <i data-lucide="settings" class="w-3.5 h-3.5"></i> Kelola Kuis
                            </a>
                            <a href="{{ route('admin.kuis.peserta', $quiz->id) }}" 
                               class="btn btn-secondary text-xs font-semibold py-1.5 px-3 rounded-xl border border-[var(--border)] text-[var(--text-primary)] hover:border-purple-400 inline-flex items-center gap-1.5">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-purple-600"></i> Hasil
                            </a>
                            <form action="{{ route('admin.materi.destroy', $quiz->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kuis ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="p-2 text-[var(--text-secondary)] hover:text-danger hover:bg-danger/5 rounded-xl border border-transparent hover:border-danger/20 transition-all" 
                                    title="Hapus Kuis">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- SECTION 3: PENUGASAN --}}
    <div id="section-tugas" class="space-y-4 pt-2">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-[var(--text-primary)] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="file-check-2" class="w-4 h-4 text-amber-500"></i>
                <span>Penugasan & Praktik Kasus HUKDIS</span>
                <span class="text-xs text-[var(--text-secondary)] font-normal">({{ $tugasItems->count() }} tugas)</span>
            </h3>
            <a href="{{ route('admin.pelatihan.tugas.create', $pelatihan->id) }}" class="text-xs font-medium text-amber-600 hover:underline inline-flex items-center gap-1">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Tugas
            </a>
        </div>

        @if($tugasItems->isEmpty())
            <div class="bg-[var(--card)] border border-dashed border-[var(--border)] rounded-2xl p-8 text-center">
                <i data-lucide="file-up" class="w-8 h-8 text-[var(--text-muted)] mx-auto mb-2 opacity-50"></i>
                <p class="text-xs text-[var(--text-secondary)] font-medium">Belum ada penugasan atau slot upload berkas.</p>
                <a href="{{ route('admin.pelatihan.tugas.create', $pelatihan->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600 mt-2 hover:underline">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Buat Penugasan Pertama
                </a>
            </div>
        @else
            <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl divide-y divide-[var(--border)] overflow-hidden shadow-xs">
                @foreach($tugasItems as $tugas)
                    <div class="p-4 flex items-center justify-between hover:bg-[var(--muted)]/30 transition-all group">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <span class="w-7 h-7 rounded-xl bg-[var(--muted)] text-[var(--text-secondary)] flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $tugas->urutan }}
                            </span>

                            <div class="w-9 h-9 rounded-xl {{ $tugas->tipe === 'upload_sertifikat' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600' }} flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $tugas->tipe === 'upload_sertifikat' ? 'award' : 'file-text' }}" class="w-4 h-4"></i>
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-semibold text-[var(--text-primary)] group-hover:text-amber-600 transition-colors truncate">
                                        {{ $tugas->judul }}
                                    </h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $tugas->tipe === 'upload_sertifikat' ? 'bg-blue-500/10 text-blue-700 border border-blue-500/20' : 'bg-amber-500/10 text-amber-700 border border-amber-500/20' }}">
                                        {{ $tugas->tipe_label }}
                                    </span>
                                </div>
                                <p class="text-xs text-[var(--text-secondary)] mt-0.5 flex items-center gap-2">
                                    <span>Skala Nilai: {{ $tugas->bobot_nilai }}</span>
                                    <span>•</span>
                                    <span>Deadline: {{ $tugas->deadline ? $tugas->deadline->format('d M Y, H:i') : 'Tanpa batas waktu' }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('admin.tugas.submissions', $tugas->id) }}" 
                               class="btn btn-primary text-white text-xs font-semibold py-1.5 px-3 rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5">
                                <i data-lucide="check-square" class="w-3.5 h-3.5"></i> Penilaian ({{ $tugas->submissions_count }})
                            </a>
                            <a href="{{ route('admin.tugas.edit', $tugas->id) }}" 
                               class="p-2 text-[var(--text-secondary)] hover:text-amber-600 hover:bg-amber-500/5 rounded-xl border border-transparent hover:border-amber-300 transition-all" 
                               title="Edit Penugasan">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.tugas.destroy', $tugas->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus penugasan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="p-2 text-[var(--text-secondary)] hover:text-danger hover:bg-danger/5 rounded-xl border border-transparent hover:border-danger/20 transition-all" 
                                    title="Hapus Penugasan">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- SECTION 4: BOBOT KELULUSAN (TAB) --}}
    <div id="section-bobot" class="max-w-2xl mx-auto py-2 hidden">
        <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-[var(--border)]">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="sliders" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[var(--text-primary)] text-base">Konfigurasi Bobot Nilai Akhir</h3>
                    <p class="text-xs text-[var(--text-secondary)]">Atur proporsi persentase evaluasi. Total seluruh bobot harus tepat 100%.</p>
                </div>
            </div>

            <form action="{{ route('admin.pelatihan.gradebook.bobot', $pelatihan->id) }}" method="POST" class="space-y-4" id="formBobotTab">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1.5">
                            Bobot Pretest (%)
                        </label>
                        <input type="number" name="bobot_pretest" min="0" max="100" value="{{ $bobot->bobot_pretest }}" required
                            class="bobot-input w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1.5">
                            Bobot Kuis Formatif (%)
                        </label>
                        <input type="number" name="bobot_quiz" min="0" max="100" value="{{ $bobot->bobot_quiz }}" required
                            class="bobot-input w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1.5">
                            Bobot Penugasan / Kasus (%)
                        </label>
                        <input type="number" name="bobot_tugas" min="0" max="100" value="{{ $bobot->bobot_tugas }}" required
                            class="bobot-input w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1.5">
                            Bobot Posttest / Ujian Akhir (%)
                        </label>
                        <input type="number" name="bobot_posttest" min="0" max="100" value="{{ $bobot->bobot_posttest }}" required
                            class="bobot-input w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-[var(--border)]">
                        <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1.5">
                            Passing Grade Kelulusan (Nilai Minimum Lulus)
                        </label>
                        <input type="number" name="passing_grade" min="0" max="100" value="{{ $bobot->passing_grade }}" required
                            class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-[var(--border)]">
                    <div class="text-xs">
                        <span class="text-[var(--text-secondary)]">Total Bobot: </span>
                        <span id="tabTotalBobot" class="font-bold text-emerald-600">100%</span>
                    </div>

                    <button type="submit" class="btn btn-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-xs transition-all">
                        Simpan Bobot Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- MODAL TAMBAH KONTEN (HIDDEN BY DEFAULT) --}}
<div id="modalAddContent" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)] bg-[var(--muted)]/20">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[var(--text-primary)] text-base">Pilih Jenis Konten</h3>
                    <p class="text-xs text-[var(--text-secondary)]">Pilih format aktivitas pembelajaran yang ingin ditambahkan.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddContentModal()" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] p-1.5 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-5 space-y-3">
            {{-- 1. Materi Pembelajaran --}}
            <a href="{{ route('admin.pelatihan.materi.create', $pelatihan->id) }}" 
               class="flex items-start gap-4 p-4 rounded-xl border border-[var(--border)] hover:border-primary/50 hover:bg-primary/5 transition-all group">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 mt-0.5">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm text-[var(--text-primary)] group-hover:text-primary transition-colors">Materi / Modul Pembelajaran</h4>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-[var(--text-muted)] group-hover:translate-x-1 transition-all"></i>
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] mt-1">Bahan bacaan artikel, dokumen PDF peraturan disiplin, atau tautan referensi web.</p>
                </div>
            </a>

            {{-- 2. Kuis & Ujian --}}
            <a href="{{ route('admin.pelatihan.materi.create', ['pelatihan' => $pelatihan->id, 'jenis' => 'quiz']) }}" 
               class="flex items-start gap-4 p-4 rounded-xl border border-[var(--border)] hover:border-purple-500/50 hover:bg-purple-500/5 transition-all group">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 mt-0.5">
                    <i data-lucide="help-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm text-[var(--text-primary)] group-hover:text-purple-600 transition-colors">Kuis / Evaluasi / Posttest</h4>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-[var(--text-muted)] group-hover:translate-x-1 transition-all"></i>
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] mt-1">Ujian pilihan ganda, pretest awal, kuis formatif per modul, atau posttest akhir.</p>
                </div>
            </a>

            {{-- 3. Penugasan --}}
            <a href="{{ route('admin.pelatihan.tugas.create', $pelatihan->id) }}" 
               class="flex items-start gap-4 p-4 rounded-xl border border-[var(--border)] hover:border-amber-500/50 hover:bg-amber-500/5 transition-all group">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm text-[var(--text-primary)] group-hover:text-amber-600 transition-colors">Penugasan / Upload Dokumen</h4>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-[var(--text-muted)] group-hover:translate-x-1 transition-all"></i>
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] mt-1">Penugasan telaah kasus disiplin ASN atau slot upload sertifikat webinar eksternal.</p>
                </div>
            </a>
        </div>

        <div class="p-4 border-t border-[var(--border)] bg-[var(--muted)]/20 text-right">
            <button type="button" onclick="closeAddContentModal()" class="btn btn-secondary text-xs px-4 py-2 rounded-xl">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- MODAL UBAH BOBOT (HIDDEN BY DEFAULT) --}}
<div id="modalBobot" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 border-b border-[var(--border)] bg-[var(--muted)]/20">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[var(--text-primary)] text-base">Ubah Bobot Kelulusan</h3>
                    <p class="text-xs text-[var(--text-secondary)]">Total seluruh bobot harus tepat 100%.</p>
                </div>
            </div>
            <button type="button" onclick="closeBobotModal()" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] p-1.5 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.pelatihan.gradebook.bobot', $pelatihan->id) }}" method="POST" class="p-5 space-y-4" id="formBobotModal">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1">
                        Pretest (%)
                    </label>
                    <input type="number" name="bobot_pretest" min="0" max="100" value="{{ $bobot->bobot_pretest }}" required
                        class="modal-bobot-input w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1">
                        Kuis Formatif (%)
                    </label>
                    <input type="number" name="bobot_quiz" min="0" max="100" value="{{ $bobot->bobot_quiz }}" required
                        class="modal-bobot-input w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1">
                        Penugasan (%)
                    </label>
                    <input type="number" name="bobot_tugas" min="0" max="100" value="{{ $bobot->bobot_tugas }}" required
                        class="modal-bobot-input w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1">
                        Posttest (%)
                    </label>
                    <input type="number" name="bobot_posttest" min="0" max="100" value="{{ $bobot->bobot_posttest }}" required
                        class="modal-bobot-input w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <div class="col-span-2 pt-2 border-t border-[var(--border)]">
                    <label class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1">
                        Passing Grade Kelulusan
                    </label>
                    <input type="number" name="passing_grade" min="0" max="100" value="{{ $bobot->passing_grade }}" required
                        class="w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-[var(--border)]">
                <div class="text-xs">
                    <span class="text-[var(--text-secondary)]">Total Bobot: </span>
                    <span id="modalTotalBobot" class="font-bold text-emerald-600">100%</span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeBobotModal()" class="btn btn-secondary text-xs px-4 py-2 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Tab Switching
function switchTab(tab) {
    const tabs = ['semua', 'materi', 'kuis', 'tugas', 'bobot'];
    
    // Update tab buttons
    tabs.forEach(t => {
        const btn = document.getElementById(`tabBtn-${t}`);
        const badge = btn.querySelector('.tab-badge');
        if (t === tab) {
            btn.className = 'tab-btn bg-primary text-white shadow-xs px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2';
            if (badge) badge.className = 'tab-badge bg-white/20 text-white px-1.5 py-0.2 rounded-md text-[10px] font-bold';
        } else {
            btn.className = 'tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2';
            if (badge) badge.className = 'tab-badge bg-[var(--muted)] text-[var(--text-secondary)] px-1.5 py-0.2 rounded-md text-[10px] font-bold';
        }
    });

    // Content sections
    const secMateri = document.getElementById('section-materi');
    const secKuis = document.getElementById('section-kuis');
    const secTugas = document.getElementById('section-tugas');
    const secBobot = document.getElementById('section-bobot');

    // Action buttons
    const actMateri = document.getElementById('tabAction-materi');
    const actKuis = document.getElementById('tabAction-kuis');
    const actTugas = document.getElementById('tabAction-tugas');

    actMateri.classList.add('hidden');
    actKuis.classList.add('hidden');
    actTugas.classList.add('hidden');

    if (tab === 'semua') {
        secMateri.classList.remove('hidden');
        secKuis.classList.remove('hidden');
        secTugas.classList.remove('hidden');
        secBobot.classList.add('hidden');
    } else if (tab === 'materi') {
        secMateri.classList.remove('hidden');
        secKuis.classList.add('hidden');
        secTugas.classList.add('hidden');
        secBobot.classList.add('hidden');
        actMateri.classList.remove('hidden');
    } else if (tab === 'kuis') {
        secMateri.classList.add('hidden');
        secKuis.classList.remove('hidden');
        secTugas.classList.add('hidden');
        secBobot.classList.add('hidden');
        actKuis.classList.remove('hidden');
    } else if (tab === 'tugas') {
        secMateri.classList.add('hidden');
        secKuis.classList.add('hidden');
        secTugas.classList.remove('hidden');
        secBobot.classList.add('hidden');
        actTugas.classList.remove('hidden');
    } else if (tab === 'bobot') {
        secMateri.classList.add('hidden');
        secKuis.classList.add('hidden');
        secTugas.classList.add('hidden');
        secBobot.classList.remove('hidden');
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Modals
function openAddContentModal() {
    document.getElementById('modalAddContent').classList.remove('hidden');
    if (window.lucide) window.lucide.createIcons();
}
function closeAddContentModal() {
    document.getElementById('modalAddContent').classList.add('hidden');
}

function openBobotModal() {
    document.getElementById('modalBobot').classList.remove('hidden');
    if (window.lucide) window.lucide.createIcons();
}
function closeBobotModal() {
    document.getElementById('modalBobot').classList.add('hidden');
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    const modalAdd = document.getElementById('modalAddContent');
    const modalBobot = document.getElementById('modalBobot');
    if (e.target === modalAdd) closeAddContentModal();
    if (e.target === modalBobot) closeBobotModal();
});

// Close modals on Escape key
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddContentModal();
        closeBobotModal();
    }
});

// Calculate Bobot Totals
document.addEventListener('DOMContentLoaded', function() {
    function calculateTotal(containerClass, resultId) {
        const inputs = document.querySelectorAll(containerClass);
        const resultEl = document.getElementById(resultId);
        if (!inputs.length || !resultEl) return;

        function update() {
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value || 0);
            });
            resultEl.innerText = `${total}%`;
            if (Math.abs(total - 100) < 0.01) {
                resultEl.className = 'font-bold text-emerald-600 dark:text-emerald-400';
            } else {
                resultEl.className = 'font-bold text-rose-600 dark:text-rose-400';
            }
        }

        inputs.forEach(input => input.addEventListener('input', update));
        update();
    }

    calculateTotal('.bobot-input', 'tabTotalBobot');
    calculateTotal('.modal-bobot-input', 'modalTotalBobot');
});
</script>
@endpush
@endsection
