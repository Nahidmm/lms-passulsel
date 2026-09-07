@extends('layouts.app')

@section('title', 'Buat Kuis Evaluasi — ' . $pelatihan->judul)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Top Back Link & Breadcrumb --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}"
           class="text-xs font-semibold text-[var(--text-secondary)] hover:text-primary flex items-center gap-1.5 transition-colors group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i>
            <span>Kembali ke Kurikulum Pelatihan</span>
        </a>
        <div class="flex items-center gap-2 text-xs text-[var(--text-secondary)]">
            <span class="font-medium">Langkah 1 dari 2:</span>
            <span class="font-bold text-primary">Konfigurasi Kuis</span>
            <span>&rarr;</span>
            <span class="text-[var(--text-muted)]">Input Butir Soal</span>
        </div>
    </div>

    {{-- Header Banner --}}
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <i data-lucide="help-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-[var(--text-primary)]">Buat Kuis & Evaluasi Baru</h1>
                <p class="text-xs text-[var(--text-secondary)] mt-1">
                    Konfigurasi parameter evaluasi, durasi, ambang batas kelulusan, dan pengawasan integritas ujian untuk:
                    <strong class="text-[var(--text-primary)] font-semibold">{{ $pelatihan->judul }}</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- Error Flash --}}
    @if(isset($errors) && $errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 p-4 rounded-2xl text-sm flex gap-3 items-start">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-rose-600"></i>
            <div>
                <p class="font-bold text-xs uppercase tracking-wider mb-1">Harap periksa kesalahan input berikut:</p>
                <ul class="list-disc pl-4 space-y-0.5 text-xs">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('admin.pelatihan.materi.store', $pelatihan->id) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="jenis" value="quiz">
        <input type="hidden" name="durasi_baca" value="0">

        {{-- SECTION 1: Informasi Utama --}}
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Informasi Dasar Kuis</h2>
                    <p class="text-xs text-[var(--text-secondary)]">Nama ujian dan instruksi pengerjaan untuk peserta</p>
                </div>
            </div>

            <div class="space-y-4 pt-1">
                {{-- Judul Kuis --}}
                <div>
                    <label for="judul" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Judul Kuis / Ujian <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                        placeholder="Contoh: Evaluasi Pemahaman Modul 1 / Ujian Post-Test HUKDIS"
                        class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>

                {{-- Instruksi --}}
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                        Petunjuk Pengerjaan untuk Peserta <span class="text-xs font-normal text-[var(--text-muted)] lowercase">(opsional — tampil sebelum kuis dimulai)</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        placeholder="Contoh: Bacalah setiap butir soal dengan cermat. Dilarang membuka tab lain selama ujian berlangsung..."
                        class="w-full px-4 py-3 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none transition-all">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    {{-- Urutan --}}
                    <div>
                        <label for="urutan" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Urutan di Kurikulum <span class="text-danger">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number" id="urutan" name="urutan"
                                value="{{ old('urutan', $pelatihan->materis->count() + 1) }}"
                                required min="1"
                                class="w-24 px-3.5 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-sm font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                            <span class="text-xs text-[var(--text-secondary)]">
                                Urutan saat ini ke-<strong>{{ $pelatihan->materis->count() + 1 }}</strong> dari {{ $pelatihan->materis->count() }} modul.
                            </span>
                        </div>
                    </div>

                    {{-- Posttest Checkbox Card --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">
                            Klasifikasi Evaluasi
                        </label>
                        <label class="flex items-center gap-3 p-2.5 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-colors">
                            <input type="checkbox" name="is_posttest" value="1" {{ old('is_posttest') ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer shrink-0">
                            <div>
                                <p class="text-xs font-bold text-[var(--text-primary)]">Tandai sebagai Post-Test Akhir</p>
                                <p class="text-[11px] text-[var(--text-secondary)]">Nilai kuis ini dihitung khusus pada kolom Post-Test Gradebook.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: Penilaian & Waktu --}}
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Parameter Penilaian & Durasi Waktu</h2>
                    <p class="text-xs text-[var(--text-secondary)]">Tentukan ambang nilai lulus, durasi pengerjaan, dan batas percobaan peserta</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5 pt-1">
                {{-- Passing Grade --}}
                <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                        <i data-lucide="target" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <label for="passing_grade" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Nilai Lulus
                        </label>
                        <div class="relative mt-1.5 max-w-[120px] mx-auto">
                            <input type="number" id="passing_grade" name="passing_grade"
                                value="{{ old('passing_grade', 70) }}" min="0" max="100" required
                                class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-[var(--text-secondary)]">%</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-[var(--text-muted)]">Skala 0 – 100</p>
                </div>

                {{-- Durasi Menit --}}
                <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <label for="durasi_menit" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Durasi Ujian
                        </label>
                        <div class="relative mt-1.5 max-w-[120px] mx-auto">
                            <input type="number" id="durasi_menit" name="durasi_menit"
                                value="{{ old('durasi_menit', 30) }}" min="0" required
                                class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        </div>
                    </div>
                    <p class="text-[11px] text-[var(--text-muted)]">Menit (0 = Bebas)</p>
                </div>

                {{-- Max Attempts --}}
                <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <label for="max_attempts" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Maks. Percobaan
                        </label>
                        <div class="relative mt-1.5 max-w-[120px] mx-auto">
                            <input type="number" id="max_attempts" name="max_attempts"
                                value="{{ old('max_attempts', 3) }}" min="0" required
                                class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        </div>
                    </div>
                    <p class="text-[11px] text-[var(--text-muted)]">Kali (0 = Tak Terbatas)</p>
                </div>

                {{-- Poin Maks --}}
                <div class="p-4 rounded-xl bg-[var(--muted)]/40 border border-[var(--border)] text-center space-y-2">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto">
                        <i data-lucide="award" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <label for="poin" class="block text-xs font-bold uppercase tracking-wider text-[var(--text-secondary)]">
                            Poin Maksimal
                        </label>
                        <div class="relative mt-1.5 max-w-[120px] mx-auto">
                            <input type="number" id="poin" name="poin"
                                value="{{ old('poin', 100) }}" min="0" required
                                class="w-full px-2 py-1.5 bg-[var(--card)] border border-[var(--border)] rounded-lg text-lg font-bold text-center text-purple-600 dark:text-purple-400 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        </div>
                    </div>
                    <p class="text-[11px] text-[var(--text-muted)]">Skala Poin Gamifikasi</p>
                </div>
            </div>
        </div>

        {{-- SECTION 3: Perilaku Soal & Integritas Ujian --}}
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <i data-lucide="shuffle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Pengaturan Soal & Integritas</h2>
                    <p class="text-xs text-[var(--text-secondary)]">Atur pengacakan butir soal, kunci jawaban, dan pengawasan proctoring</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                {{-- Acak Soal --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all">
                    <input type="checkbox" name="acak_soal" value="1" {{ old('acak_soal', true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer mt-0.5 shrink-0">
                    <div>
                        <p class="text-xs font-bold text-[var(--text-primary)]">Acak Urutan Butir Soal</p>
                        <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Soal ditampilkan acak untuk setiap peserta ujian.</p>
                    </div>
                </label>

                {{-- Acak Pilihan Jawaban --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all">
                    <input type="checkbox" name="acak_jawaban" value="1" {{ old('acak_jawaban', true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer mt-0.5 shrink-0">
                    <div>
                        <p class="text-xs font-bold text-[var(--text-primary)]">Acak Pilihan Opsi (A, B, C, D)</p>
                        <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Posisi opsi jawaban diacak pada soal pilihan ganda.</p>
                    </div>
                </label>

                {{-- Tampilkan Feedback & Kunci --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all">
                    <input type="checkbox" name="tampilkan_feedback" value="1" {{ old('tampilkan_feedback', true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-primary border-[var(--border)] focus:ring-primary cursor-pointer mt-0.5 shrink-0">
                    <div>
                        <p class="text-xs font-bold text-[var(--text-primary)]">Tampilkan Kunci & Pembahasan</p>
                        <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Peserta dapat meninjau pembahasan setelah ujian selesai.</p>
                    </div>
                </label>

                {{-- Strict Anti-Cheat Proctoring --}}
                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-rose-500/30 bg-rose-500/5 hover:bg-rose-500/10 cursor-pointer transition-all">
                    <input type="checkbox" name="strict_anti_cheat" value="1" {{ old('strict_anti_cheat', false) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-rose-600 border-rose-400 focus:ring-rose-500 cursor-pointer mt-0.5 shrink-0">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-xs font-bold text-rose-700 dark:text-rose-400">Aktifkan Anti-Cheat Proctoring</p>
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-500/20 text-rose-700 dark:text-rose-300">Ketat</span>
                        </div>
                        <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Mendeteksi pindah tab / minimize browser, otomatis submit pada pelanggaran ke-3.</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- SECTION 4: Mode Tampilan Ujian --}}
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-[var(--border)]">
                <div class="w-8 h-8 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                    <i data-lucide="monitor" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Mode Pengalaman Ujian</h2>
                    <p class="text-xs text-[var(--text-secondary)]">Pilih tata letak antarmuka ujian untuk peserta</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                {{-- Mode Standard --}}
                <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-primary bg-primary/5" id="labelModeStandard" onclick="setExamMode('standard')">
                    <input type="radio" name="mode_tampilan" value="standard" class="hidden" checked id="radioModeStandard">
                    <div class="w-4 h-4 rounded-full border-2 border-primary mt-0.5 flex items-center justify-center shrink-0" id="radioCircleStandard">
                        <div class="w-2 h-2 rounded-full bg-primary" id="radioDotStandard"></div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[var(--text-primary)]">Mode Standar Akademik (Direkomendasikan)</p>
                        <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Tampilan formal ujian kedinasan, bebas distraksi, bersih dan fokus pada butir soal.</p>
                    </div>
                </label>

                {{-- Mode Interaktif --}}
                <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all" id="labelModeInteraktif" onclick="setExamMode('interaktif')">
                    <input type="radio" name="mode_tampilan" value="interaktif" class="hidden" id="radioModeInteraktif">
                    <div class="w-4 h-4 rounded-full border-2 border-[var(--text-muted)] mt-0.5 flex items-center justify-center shrink-0" id="radioCircleInteraktif">
                        <div class="w-2 h-2 rounded-full bg-violet-600 hidden" id="radioDotInteraktif"></div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[var(--text-primary)]">Mode Interaktif / Gamifikasi</p>
                        <p class="text-[11px] text-[var(--text-secondary)] mt-0.5">Dilengkapi efek suara, animasi visual feedback, dan papan peringkat (Leaderboard).</p>
                    </div>
                </label>
            </div>

            {{-- Extra Interactive Options (Hidden by default) --}}
            <div id="interactiveOptions" class="hidden pt-4 border-t border-[var(--border)] space-y-3 animate-in fade-in duration-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="sub_mode" class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1">Sub Mode</label>
                        <select id="sub_mode" name="sub_mode" class="w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="standard">Standard Interactive</option>
                            <option value="time_attack">Time Attack</option>
                            <option value="practice">Practice Mode</option>
                        </select>
                    </div>
                    <div>
                        <label for="timer_per_soal" class="block text-xs font-semibold text-[var(--text-secondary)] uppercase mb-1">Waktu Per Soal (Detik)</label>
                        <input type="number" id="timer_per_soal" name="timer_per_soal" value="0" min="0"
                            class="w-full px-3 py-2 bg-[var(--input)] border border-[var(--border)] rounded-xl text-xs text-[var(--text-primary)] focus:ring-2 focus:ring-primary/20 outline-none">
                        <p class="text-[10px] text-[var(--text-muted)] mt-0.5">0 = Menggunakan total durasi ujian</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-2">
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-[var(--border)] text-xs cursor-pointer">
                        <input type="checkbox" name="sound_enabled" value="1" checked class="w-3.5 h-3.5 text-primary rounded">
                        <span>Efek Suara</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-[var(--border)] text-xs cursor-pointer">
                        <input type="checkbox" name="animasi_enabled" value="1" checked class="w-3.5 h-3.5 text-primary rounded">
                        <span>Animasi</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-[var(--border)] text-xs cursor-pointer">
                        <input type="checkbox" name="leaderboard_enabled" value="1" class="w-3.5 h-3.5 text-primary rounded">
                        <span>Leaderboard</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-[var(--border)] text-xs cursor-pointer">
                        <input type="checkbox" name="bonus_kecepatan_enabled" value="1" class="w-3.5 h-3.5 text-primary rounded">
                        <span>Bonus Waktu</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- SECTION 5: Status Publikasi --}}
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-5">
            <label class="flex items-center justify-between gap-4 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <i data-lucide="globe" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[var(--text-primary)]">Langsung Publikasikan Kuis</p>
                        <p class="text-[11px] text-[var(--text-secondary)]">Kuis langsung aktif di dalam kurikulum pelatihan setelah soal dibuat.</p>
                    </div>
                </div>
                <input type="checkbox" name="is_active" value="1" checked
                    class="w-5 h-5 rounded text-emerald-600 border-[var(--border)] focus:ring-emerald-500 cursor-pointer shrink-0">
            </label>
        </div>

        {{-- Bottom Actions --}}
        <div class="flex items-center justify-between gap-4 pt-2">
            <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}"
               class="btn btn-secondary text-[var(--text-primary)] text-xs font-semibold py-2.5 px-5 rounded-xl border border-[var(--border)] transition-all">
                Batal
            </a>

            <button type="submit"
                class="btn btn-primary text-white text-xs font-semibold py-2.5 px-6 rounded-xl shadow-xs transition-all flex items-center gap-2">
                <span>Simpan & Lanjut ke Input Soal</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
function setExamMode(mode) {
    const radioStandard = document.getElementById('radioModeStandard');
    const radioInteraktif = document.getElementById('radioModeInteraktif');
    const labelStandard = document.getElementById('labelModeStandard');
    const labelInteraktif = document.getElementById('labelModeInteraktif');
    const circleStandard = document.getElementById('radioCircleStandard');
    const circleInteraktif = document.getElementById('radioCircleInteraktif');
    const dotStandard = document.getElementById('radioDotStandard');
    const dotInteraktif = document.getElementById('radioDotInteraktif');
    const interactiveOptions = document.getElementById('interactiveOptions');

    if (mode === 'standard') {
        radioStandard.checked = true;
        labelStandard.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-primary bg-primary/5';
        labelInteraktif.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all';
        circleStandard.className = 'w-4 h-4 rounded-full border-2 border-primary mt-0.5 flex items-center justify-center shrink-0';
        circleInteraktif.className = 'w-4 h-4 rounded-full border-2 border-[var(--text-muted)] mt-0.5 flex items-center justify-center shrink-0';
        dotStandard.classList.remove('hidden');
        dotInteraktif.classList.add('hidden');
        interactiveOptions.classList.add('hidden');
    } else {
        radioInteraktif.checked = true;
        labelInteraktif.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-violet-600 bg-violet-600/5';
        labelStandard.className = 'relative flex items-start gap-3 p-4 rounded-xl border-2 border-[var(--border)] hover:bg-[var(--muted)]/40 cursor-pointer transition-all';
        circleInteraktif.className = 'w-4 h-4 rounded-full border-2 border-violet-600 mt-0.5 flex items-center justify-center shrink-0';
        circleStandard.className = 'w-4 h-4 rounded-full border-2 border-[var(--text-muted)] mt-0.5 flex items-center justify-center shrink-0';
        dotInteraktif.classList.remove('hidden');
        dotStandard.classList.add('hidden');
        interactiveOptions.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection
