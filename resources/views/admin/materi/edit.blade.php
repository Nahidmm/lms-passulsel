@extends('layouts.app')

@section('title', $materi->jenis === 'quiz' ? 'Kelola Kuis: ' . $materi->judul : 'Edit Materi: ' . $materi->judul)

@section('content')

@if($materi->jenis === 'quiz')
{{-- ========================================================================= --}}
{{-- QUIZ MANAGEMENT (CLEAN TABBED INTERFACE)                                 --}}
{{-- ========================================================================= --}}
<div class="space-y-6">

    {{-- Top Navigation & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            @if($materi->is_pretest)
                <a href="{{ route('admin.pretest.index') }}"
                   class="text-xs font-semibold text-[var(--text-secondary)] hover:text-primary inline-flex items-center gap-1.5 transition-colors group">
                    <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                    <span>Kembali ke Bank Soal Pretest</span>
                </a>
            @else
                <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}"
                   class="text-xs font-semibold text-[var(--text-secondary)] hover:text-primary inline-flex items-center gap-1.5 transition-colors group">
                    <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
                    <span>Kembali ke Kurikulum Pelatihan</span>
                </a>
            @endif
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <a href="{{ route('admin.materi.preview-quiz', $materi->id) }}" target="_blank"
               class="btn btn-secondary text-xs font-medium py-2 px-3.5 rounded-xl border border-[var(--border)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-all inline-flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Preview Kuis</span>
            </a>

            <a href="{{ route('admin.soal.import', $materi->id) }}"
               class="btn btn-secondary text-xs font-medium py-2 px-3.5 rounded-xl border border-[var(--border)] text-[var(--text-primary)] hover:border-primary/40 transition-all inline-flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="upload-cloud" class="w-3.5 h-3.5 text-primary"></i>
                <span>Import Soal (Excel)</span>
            </a>

            <a href="{{ route('admin.kuis.peserta', $materi->id) }}"
               class="btn btn-secondary text-xs font-semibold py-2 px-3.5 rounded-xl border border-[var(--border)] text-[var(--text-primary)] hover:border-purple-400 transition-all inline-flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-purple-600"></i>
                <span>Hasil & Nilai Peserta</span>
            </a>

            <a href="{{ route('admin.materi.soal.create', $materi->id) }}"
               class="btn btn-primary text-white text-xs font-semibold py-2 px-4 rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Soal Baru</span>
            </a>
        </div>
    </div>

    {{-- Quiz Header Card --}}
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center flex-wrap gap-2.5">
                    @if($materi->is_pretest)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">Pretest Diagnostik</span>
                    @elseif($materi->is_posttest)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Posttest / Evaluasi Akhir</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">Kuis Formatif</span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $materi->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $materi->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $materi->is_active ? 'Published' : 'Draft' }}
                    </span>

                    @if($materi->strict_anti_cheat)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/10 text-rose-600 border border-rose-500/20">Proctoring Aktif</span>
                    @endif
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">{{ $materi->judul }}</h1>
                @if($materi->deskripsi)
                    <p class="text-xs text-[var(--text-secondary)] max-w-2xl">{{ $materi->deskripsi }}</p>
                @endif
            </div>
        </div>

        {{-- Stats Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-5 mt-5 border-t border-[var(--border)]">
            <div class="p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-secondary)]">Total Soal</p>
                    <p class="text-sm font-bold text-[var(--text-primary)]">{{ $materi->soals->count() }} Soal</p>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <i data-lucide="award" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-secondary)]">Total Poin Bobot</p>
                    <p class="text-sm font-bold text-[var(--text-primary)]">{{ $materi->soals->sum('bobot') }} Poin</p>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="target" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-secondary)]">Nilai Lulus</p>
                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $materi->passing_grade ?? 70 }}%</p>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-secondary)]">Durasi Ujian</p>
                    <p class="text-sm font-bold text-[var(--text-primary)]">{{ $materi->durasi_menit ? $materi->durasi_menit . ' Menit' : 'Bebas' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Feedback Alerts --}}
    @if(isset($errors) && $errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 p-4 rounded-2xl text-sm flex gap-3 items-start">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-rose-600"></i>
            <div>
                <p class="font-bold text-xs uppercase tracking-wider mb-1">Terjadi kesalahan input:</p>
                <ul class="list-disc pl-4 space-y-0.5 text-xs">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-2xl text-sm flex items-center gap-3">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Segmented Tabs --}}
    <div class="flex items-center justify-between border-b border-[var(--border)] pb-2">
        <div class="flex items-center gap-2">
            <button type="button" onclick="switchQuizTab('soal')" id="btnTabSoal"
                class="tab-btn bg-primary text-white shadow-xs px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <i data-lucide="list-checks" class="w-4 h-4"></i>
                <span>Daftar Butir Soal</span>
                <span class="tab-badge bg-white/20 text-white px-1.5 py-0.2 rounded-md text-[10px] font-bold">
                    {{ $materi->soals->count() }}
                </span>
            </button>

            <button type="button" onclick="switchQuizTab('pengaturan')" id="btnTabPengaturan"
                class="tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                <i data-lucide="settings" class="w-4 h-4"></i>
                <span>Konfigurasi & Pengaturan Kuis</span>
            </button>
        </div>

        <div id="tabActionSoal">
            <a href="{{ route('admin.materi.soal.create', $materi->id) }}"
               class="btn btn-primary text-white text-xs font-semibold py-2 px-3.5 rounded-xl shadow-xs transition-all inline-flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Tambah Soal</span>
            </a>
        </div>
    </div>

    {{-- TAB 1: DAFTAR BUTIR SOAL (HERO CONTENT) --}}
    <div id="tabContentSoal" class="space-y-4">
        @php
            $tipeLabels = [
                'pilihan_ganda' => ['label' => 'Pilihan Ganda (Single)', 'color' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20'],
                'multi_select'  => ['label' => 'Multiple Select', 'color' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20'],
                'essay'         => ['label' => 'Essay / Uraian', 'color' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20'],
                'isian_singkat' => ['label' => 'Isian Singkat', 'color' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20'],
                'menjodohkan'   => ['label' => 'Menjodohkan', 'color' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20'],
            ];
        @endphp

        @if($materi->soals->isEmpty())
            <div class="bg-[var(--card)] border border-dashed border-[var(--border)] rounded-2xl p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="help-circle" class="w-7 h-7"></i>
                </div>
                <h3 class="font-bold text-[var(--text-primary)] text-base">Belum Ada Soal di Kuis Ini</h3>
                <p class="text-xs text-[var(--text-secondary)] mt-1 mb-5 max-w-sm mx-auto">
                    Kuis ini belum memiliki pertanyaan. Anda dapat menambahkan soal secara manual atau mengimport sekaligus dari file Excel.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('admin.materi.soal.create', $materi->id) }}"
                       class="btn btn-primary text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-xs inline-flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Soal Pertama
                    </a>
                    <a href="{{ route('admin.soal.import', $materi->id) }}"
                       class="btn btn-secondary text-xs font-semibold py-2.5 px-4 rounded-xl border border-[var(--border)] inline-flex items-center gap-2">
                        <i data-lucide="upload-cloud" class="w-4 h-4 text-primary"></i> Import Soal Excel
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach($materi->soals as $idx => $soal)
                    @php $t = $tipeLabels[$soal->tipe] ?? ['label' => $soal->tipe, 'color' => 'bg-slate-500/10 text-slate-600 border border-slate-500/20']; @endphp
                    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-5 shadow-2xs hover:shadow-xs transition-all space-y-3.5 group">
                        {{-- Top Header Row --}}
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                <span class="w-7 h-7 rounded-xl bg-[var(--muted)] text-[var(--text-primary)] flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $t['color'] }}">
                                            {{ $t['label'] }}
                                        </span>
                                        <span class="text-xs font-bold text-[var(--text-primary)] bg-[var(--muted)]/60 px-2 py-0.5 rounded-md">
                                            {{ $soal->bobot }} Poin
                                        </span>
                                        @if($soal->topik)
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 flex items-center gap-1">
                                                <i data-lucide="tag" class="w-2.5 h-2.5"></i>
                                                {{ $soal->topik->nama_topik }}
                                            </span>
                                        @endif
                                        @if(!$soal->is_active)
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-500/10 text-slate-500 border border-slate-500/20">Draft (Nonaktif)</span>
                                        @endif
                                    </div>
                                    <div class="text-sm font-semibold text-[var(--text-primary)] leading-relaxed pt-0.5">
                                        {!! nl2br(e($soal->pertanyaan)) !!}
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('admin.soal.edit', $soal->id) }}"
                                   class="p-2 text-[var(--text-secondary)] hover:text-primary hover:bg-primary/10 rounded-xl border border-transparent hover:border-primary/20 transition-all"
                                   title="Edit Soal">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus butir soal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-[var(--text-secondary)] hover:text-rose-600 hover:bg-rose-500/10 rounded-xl border border-transparent hover:border-rose-500/20 transition-all"
                                        title="Hapus Soal">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Options Preview by Question Type --}}
                        @if(in_array($soal->tipe, ['pilihan_ganda', 'multi_select']) && $soal->pilihanJawaban->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:pl-10 pt-1">
                                @foreach($soal->pilihanJawaban as $pil)
                                    <div class="flex items-center gap-2.5 p-3 rounded-xl text-xs border transition-all {{ $pil->is_correct ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-900 dark:text-emerald-200 font-medium' : 'bg-[var(--card)] border-[var(--border)] text-[var(--text-secondary)] hover:border-[var(--text-muted)]' }}">
                                        <span class="w-5 h-5 rounded-md flex items-center justify-center font-bold text-[10px] shrink-0 {{ $pil->is_correct ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-[var(--muted)] text-[var(--text-secondary)]' }}">
                                            {{ $pil->huruf }}
                                        </span>
                                        <span class="flex-1 truncate leading-snug {{ $pil->is_correct ? 'font-semibold text-emerald-950 dark:text-emerald-100' : 'text-[var(--text-primary)]' }}">
                                            {{ $pil->teks }}
                                        </span>
                                        @if($pil->is_correct)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded-md shrink-0">
                                                <i data-lucide="check" class="w-3 h-3 text-emerald-600 dark:text-emerald-400"></i>
                                                Kunci Benar
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($soal->tipe === 'isian_singkat')
                            <div class="sm:pl-10 pt-1">
                                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs bg-emerald-500/10 border border-emerald-500/30 text-emerald-900 dark:text-emerald-200">
                                    <i data-lucide="key-round" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                    <span class="font-bold text-emerald-700 dark:text-emerald-300">Kunci Jawaban Isian:</span>
                                    <span class="font-mono font-bold bg-white/60 dark:bg-slate-900/60 px-2 py-0.5 rounded border border-emerald-500/20 text-emerald-800 dark:text-emerald-200">{{ $soal->pilihanJawaban->first()->teks ?? '-' }}</span>
                                </div>
                            </div>
                        @elseif($soal->tipe === 'menjodohkan')
                            <div class="sm:pl-10 pt-1 space-y-2">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-secondary)]">Pasangan Kolom Yang Benar:</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($soal->pilihanJawaban as $pair)
                                        @php $parts = explode('|||', $pair->teks); @endphp
                                        <div class="flex items-center justify-between gap-2.5 p-2.5 rounded-xl border border-[var(--border)] bg-[var(--card)] text-xs">
                                            <span class="font-medium text-[var(--text-primary)]">{{ $parts[0] ?? '' }}</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-primary shrink-0"></i>
                                            <span class="font-bold text-primary">{{ $parts[1] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($soal->tipe === 'essay')
                            <div class="sm:pl-10 pt-1">
                                <div class="flex items-center gap-2 p-2.5 rounded-xl text-xs bg-[var(--muted)]/40 border border-[var(--border)] text-[var(--text-secondary)]">
                                    <i data-lucide="align-left" class="w-4 h-4 text-[var(--text-muted)] shrink-0"></i>
                                    <span>Tipe Soal Uraian / Essay. Jawaban peserta akan diperiksa dan dinilai langsung oleh instruktur.</span>
                                </div>
                            </div>
                        @endif

                        {{-- Pembahasan Callout if available --}}
                        @if($soal->pembahasan)
                            <div class="sm:pl-10 pt-1">
                                <div class="p-3 rounded-xl text-xs bg-indigo-500/5 border border-indigo-500/20 text-indigo-900 dark:text-indigo-200 flex items-start gap-2.5">
                                    <i data-lucide="help-circle" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5"></i>
                                    <div class="leading-relaxed">
                                        <strong class="font-bold text-indigo-700 dark:text-indigo-300">Pembahasan:</strong> {{ $soal->pembahasan }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- TAB 2: KONFIGURASI & PENGATURAN KUIS --}}
    <div id="tabContentPengaturan" class="space-y-6 hidden">
        <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="jenis" value="quiz">
            <input type="hidden" name="urutan" value="{{ $materi->urutan }}">
            <input type="hidden" name="durasi_baca" value="{{ $materi->durasi_baca ?? 0 }}">

            {{-- Informasi Dasar --}}
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Informasi Dasar Kuis</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Judul dan petunjuk instruksi untuk peserta</p>
                    </div>
                </div>

                <div class="space-y-4 pt-1">
                    <div>
                        <label for="judul" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Judul Kuis / Ujian <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" required
                            class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Petunjuk Pengerjaan untuk Peserta <span class="text-xs font-normal text-[var(--text-muted)] lowercase">(opsional)</span>
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="w-full px-4 py-3 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-colors">
                            <input type="checkbox" name="is_posttest" value="1" {{ old('is_posttest', $materi->is_posttest) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer shrink-0">
                            <div>
                                <p class="text-xs font-bold text-[var(--text-primary)]">Tandai sebagai Post-Test / Evaluasi Akhir</p>
                                <p class="text-[11px] text-[var(--text-secondary)]">Nilai kuis ini akan dihitung khusus ke kolom Post-Test pada Buku Nilai (Gradebook).</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Penilaian & Waktu --}}
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Penilaian, Durasi & Poin</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Tentukan ambang nilai kelulusan dan durasi pengerjaan</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5 pt-1">
                    {{-- Nilai Lulus --}}
                    <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                        <label for="passing_grade" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Nilai Lulus (%)
                        </label>
                        <input type="number" id="passing_grade" name="passing_grade"
                            value="{{ old('passing_grade', $materi->passing_grade ?? 70) }}" min="0" max="100" required
                            class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <p class="text-[11px] text-[var(--text-muted)]">Skala 0 – 100</p>
                    </div>

                    {{-- Durasi --}}
                    <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                        <label for="durasi_menit" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Durasi (Menit)
                        </label>
                        <input type="number" id="durasi_menit" name="durasi_menit"
                            value="{{ old('durasi_menit', $materi->durasi_menit ?? 30) }}" min="0" required
                            class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <p class="text-[11px] text-[var(--text-muted)]">0 = Tanpa batas</p>
                    </div>

                    {{-- Max Coba --}}
                    <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                        <label for="max_attempts" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Maks. Percobaan
                        </label>
                        <input type="number" id="max_attempts" name="max_attempts"
                            value="{{ old('max_attempts', $materi->max_attempts ?? 3) }}" min="0" required
                            class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <p class="text-[11px] text-[var(--text-muted)]">0 = Tidak terbatas</p>
                    </div>

                    {{-- Poin XP --}}
                    <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                        <label for="poin" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Poin Reward
                        </label>
                        <input type="number" id="poin" name="poin"
                            value="{{ old('poin', $materi->poin ?? 100) }}" min="0" required
                            class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-purple-600 dark:text-purple-400 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <p class="text-[11px] text-[var(--text-muted)]">Poin XP penyelesaian</p>
                    </div>
                </div>
            </div>

            {{-- Pengaturan Soal & Integritas --}}
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Perilaku Soal & Integritas Ujian</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Pengacakan soal, review jawaban, dan sistem pengawasan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all">
                        <input type="checkbox" name="acak_soal" value="1" {{ old('acak_soal', $materi->acak_soal) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer mt-0.5 shrink-0">
                        <div>
                            <p class="text-xs font-bold text-[var(--text-primary)]">Acak Urutan Soal</p>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Pertanyaan diacak setiap kali peserta mengerjakan ujian.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all">
                        <input type="checkbox" name="acak_jawaban" value="1" {{ old('acak_jawaban', $materi->acak_jawaban) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer mt-0.5 shrink-0">
                        <div>
                            <p class="text-xs font-bold text-[var(--text-primary)]">Acak Pilihan Jawaban</p>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Urutan opsi A, B, C, D diacak pada soal pilihan ganda.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all">
                        <input type="checkbox" name="tampilkan_feedback" value="1" {{ old('tampilkan_feedback', $materi->tampilkan_feedback) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer mt-0.5 shrink-0">
                        <div>
                            <p class="text-xs font-bold text-[var(--text-primary)]">Tampilkan Pembahasan</p>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Peserta dapat melihat jawaban benar setelah sesi selesai.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-rose-500/30 bg-rose-500/5 hover:bg-rose-500/10 cursor-pointer transition-all">
                        <input type="checkbox" name="strict_anti_cheat" value="1" {{ old('strict_anti_cheat', $materi->strict_anti_cheat) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-rose-600 border-rose-400 focus:ring-rose-500 cursor-pointer mt-0.5 shrink-0">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-rose-700 dark:text-rose-400">Aktifkan Anti-Cheat Proctoring</p>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-500/20 text-rose-700 dark:text-rose-300">Ketat</span>
                            </div>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Deteksi otomatis perpindahan tab, minimize browser, auto-submit pada pelanggaran ke-3.</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Mode Tampilan Ujian --}}
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                    <div class="w-8 h-8 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                        <i data-lucide="monitor" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Mode Tampilan Ujian</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Tata letak antarmuka saat peserta mengerjakan kuis</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'standard' ? 'border-primary bg-primary/5' : 'border-[var(--border)] hover:bg-[var(--muted)]/40' }}" id="labelEditStandard" onclick="setEditExamMode('standard')">
                        <input type="radio" name="mode_tampilan" value="standard" class="hidden" {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'standard' ? 'checked' : '' }} id="radioEditStandard">
                        <div class="w-4 h-4 rounded-full border-2 {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'standard' ? 'border-primary' : 'border-[var(--text-muted)]' }} mt-0.5 flex items-center justify-center shrink-0" id="circleEditStandard">
                            <div class="w-2 h-2 rounded-full bg-primary {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'standard' ? '' : 'hidden' }}" id="dotEditStandard"></div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[var(--text-primary)]">Mode Standar Akademik</p>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Tampilan ujian formal institusi, bebas distraksi, fokus pada soal.</p>
                        </div>
                    </label>

                    <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'interaktif' ? 'border-violet-600 bg-violet-600/5' : 'border-[var(--border)] hover:bg-[var(--muted)]/40' }}" id="labelEditInteraktif" onclick="setEditExamMode('interaktif')">
                        <input type="radio" name="mode_tampilan" value="interaktif" class="hidden" {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'interaktif' ? 'checked' : '' }} id="radioEditInteraktif">
                        <div class="w-4 h-4 rounded-full border-2 {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'interaktif' ? 'border-violet-600' : 'border-[var(--text-muted)]' }} mt-0.5 flex items-center justify-center shrink-0" id="circleEditInteraktif">
                            <div class="w-2 h-2 rounded-full bg-violet-600 {{ old('mode_tampilan', $materi->mode_tampilan ?? 'standard') === 'interaktif' ? '' : 'hidden' }}" id="dotEditInteraktif"></div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[var(--text-primary)]">Mode Interaktif / Gamifikasi</p>
                            <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Dilengkapi efek audio, animasi feedback, dan papan peringkat.</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Status Publikasi --}}
            <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-5">
                <label class="flex items-center justify-between gap-4 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i data-lucide="globe" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[var(--text-primary)]">Kuis Aktif (Published)</p>
                            <p class="text-[11px] text-[var(--text-secondary)]">Kuis dapat diakses oleh peserta di dalam pelatihan.</p>
                        </div>
                    </div>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $materi->is_active) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-emerald-600 border-[var(--border)] focus:ring-emerald-500 cursor-pointer shrink-0">
                </label>
            </div>

            {{-- Submit Action Bar --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit"
                    class="btn btn-primary text-white text-xs font-semibold py-2.5 px-6 rounded-xl shadow-xs transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Konfigurasi</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
// Quiz Tab Switching
function switchQuizTab(tab) {
    const btnSoal = document.getElementById('btnTabSoal');
    const btnPengaturan = document.getElementById('btnTabPengaturan');
    const badgeSoal = btnSoal.querySelector('.tab-badge');
    const contentSoal = document.getElementById('tabContentSoal');
    const contentPengaturan = document.getElementById('tabContentPengaturan');
    const actionSoal = document.getElementById('tabActionSoal');

    if (tab === 'soal') {
        btnSoal.className = 'tab-btn bg-primary text-white shadow-xs px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2';
        if (badgeSoal) badgeSoal.className = 'tab-badge bg-white/20 text-white px-1.5 py-0.2 rounded-md text-[10px] font-bold';

        btnPengaturan.className = 'tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2';

        contentSoal.classList.remove('hidden');
        contentPengaturan.classList.add('hidden');
        if (actionSoal) actionSoal.classList.remove('hidden');
    } else {
        btnPengaturan.className = 'tab-btn bg-primary text-white shadow-xs px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2';

        btnSoal.className = 'tab-btn text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--muted)]/50 px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2';
        if (badgeSoal) badgeSoal.className = 'tab-badge bg-[var(--muted)] text-[var(--text-secondary)] px-1.5 py-0.2 rounded-md text-[10px] font-bold';

        contentPengaturan.classList.remove('hidden');
        contentSoal.classList.add('hidden');
        if (actionSoal) actionSoal.classList.add('hidden');
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }
}

function setEditExamMode(mode) {
    const radioStd = document.getElementById('radioEditStandard');
    const radioInt = document.getElementById('radioEditInteraktif');
    const labelStd = document.getElementById('labelEditStandard');
    const labelInt = document.getElementById('labelEditInteraktif');
    const circleStd = document.getElementById('circleEditStandard');
    const circleInt = document.getElementById('circleEditInteraktif');
    const dotStd = document.getElementById('dotEditStandard');
    const dotInt = document.getElementById('dotEditInteraktif');

    if (mode === 'standard') {
        radioStd.checked = true;
        labelStd.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-primary bg-primary/5';
        labelInt.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all';
        circleStd.className = 'w-4 h-4 rounded-full border-2 border-primary mt-0.5 flex items-center justify-center shrink-0';
        circleInt.className = 'w-4 h-4 rounded-full border-2 border-[var(--text-muted)] mt-0.5 flex items-center justify-center shrink-0';
        dotStd.classList.remove('hidden');
        dotInt.classList.add('hidden');
    } else {
        radioInt.checked = true;
        labelInt.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-violet-600 bg-violet-600/5';
        labelStd.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all';
        circleInt.className = 'w-4 h-4 rounded-full border-2 border-violet-600 mt-0.5 flex items-center justify-center shrink-0';
        circleStd.className = 'w-4 h-4 rounded-full border-2 border-[var(--text-muted)] mt-0.5 flex items-center justify-center shrink-0';
        dotInt.classList.remove('hidden');
        dotStd.classList.add('hidden');
    }
}
</script>
@endpush

@else
{{-- ========================================================================= --}}
{{-- NON-QUIZ MATERI EDIT (CLEAN DOCUMENT/VIDEO FORM)                          --}}
{{-- ========================================================================= --}}
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}"
           class="text-xs font-semibold text-[var(--text-secondary)] hover:text-primary inline-flex items-center gap-1.5 transition-colors group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
            <span>Kembali ke Kurikulum</span>
        </a>
    </div>

    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 md:p-8 space-y-6">
        <div class="pb-4 border-b border-[var(--border)] flex items-center gap-3.5">
            @php
                $icon = match($materi->jenis) {
                    'pdf'         => 'file-text',
                    'ppt','pptx'  => 'presentation',
                    'video_embed' => 'play-circle',
                    'link'        => 'link-2',
                    default       => 'file'
                };
            @endphp
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-[var(--text-primary)]">Edit Materi Pembelajaran</h1>
                <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $materi->judul }}</p>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 p-4 rounded-2xl text-sm flex gap-3 items-start">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-rose-600"></i>
                <ul class="list-disc pl-4 space-y-0.5 text-xs">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 p-4 rounded-2xl text-sm flex items-center gap-3">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="judul" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Judul Materi <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" required
                        class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                <div>
                    <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Deskripsi / Petunjuk Baca
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full px-4 py-3 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-1">
                    <div>
                        <label for="jenis" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Jenis Konten <span class="text-danger">*</span>
                        </label>
                        <select id="jenis" name="jenis" required onchange="toggleContentInput(this.value)"
                            class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="pdf" {{ old('jenis', $materi->jenis) == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                            <option value="ppt" {{ old('jenis', $materi->jenis) == 'ppt' ? 'selected' : '' }}>PowerPoint (PPT)</option>
                            <option value="pptx" {{ old('jenis', $materi->jenis) == 'pptx' ? 'selected' : '' }}>PowerPoint (PPTX)</option>
                            <option value="video_embed" {{ old('jenis', $materi->jenis) == 'video_embed' ? 'selected' : '' }}>YouTube Embed Video</option>
                            <option value="link" {{ old('jenis', $materi->jenis) == 'link' ? 'selected' : '' }}>Tautan Eksternal</option>
                        </select>
                    </div>

                    <div>
                        <label for="urutan" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Urutan <span class="text-danger">*</span>
                        </label>
                        <input type="number" id="urutan" name="urutan" value="{{ old('urutan', $materi->urutan) }}" required min="1"
                            class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-xs font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>

                    <div>
                        <label for="durasi_baca" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Estimasi Baca (Mnt) <span class="text-danger">*</span>
                        </label>
                        <input type="number" id="durasi_baca" name="durasi_baca" value="{{ old('durasi_baca', $materi->durasi_baca) }}" required min="1"
                            class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-xs font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>

                    <div>
                        <label for="poin" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Poin Selesai <span class="text-danger">*</span>
                        </label>
                        <input type="number" id="poin" name="poin" value="{{ old('poin', $materi->poin ?? 50) }}" required min="0"
                            class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-xs font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                {{-- File Upload --}}
                <div id="file_input_container" class="{{ in_array(old('jenis', $materi->jenis), ['link', 'video_embed']) ? 'hidden' : '' }} pt-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Berkas Dokumen <span class="text-xs font-normal text-[var(--text-muted)] lowercase">(kosongkan jika tidak diganti)</span>
                    </label>
                    <input type="file" name="file_upload" accept=".pdf,.ppt,.pptx"
                        class="w-full text-xs text-[var(--text-secondary)] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary/10 file:text-primary file:font-semibold cursor-pointer border border-[var(--border)] rounded-xl p-2 bg-[var(--input)]">
                    @if($materi->file_path)
                        <p class="text-xs text-emerald-600 mt-1.5 flex items-center gap-1">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Berkas saat ini tersedia di sistem
                        </p>
                    @endif
                </div>

                {{-- URL Input --}}
                <div id="url_input_container" class="{{ in_array(old('jenis', $materi->jenis), ['link', 'video_embed']) ? '' : 'hidden' }} pt-2">
                    <label for="url_link" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Tautan / URL Eksternal <span class="text-danger">*</span>
                    </label>
                    <input type="url" id="url_link" name="url_link" value="{{ old('url_link', $materi->url_link) }}" placeholder="https://..."
                        class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Publish Status --}}
                <div class="pt-3 border-t border-[var(--border)]">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $materi->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-emerald-600 border-[var(--border)] focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs font-semibold text-[var(--text-primary)]">Materi Aktif (Dapat diakses peserta pelatihan)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[var(--border)]">
                <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}"
                   class="btn btn-secondary text-[var(--text-primary)] text-xs font-semibold py-2.5 px-5 rounded-xl border border-[var(--border)] transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="btn btn-primary text-white text-xs font-semibold py-2.5 px-6 rounded-xl shadow-xs transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Perbarui Materi</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleContentInput(jenis) {
    const f = document.getElementById('file_input_container');
    const u = document.getElementById('url_input_container');
    if (!f || !u) return;
    if (jenis === 'link' || jenis === 'video_embed') {
        f.classList.add('hidden');
        u.classList.remove('hidden');
    } else {
        u.classList.add('hidden');
        f.classList.remove('hidden');
    }
}
</script>
@endpush
@endif

@endsection
