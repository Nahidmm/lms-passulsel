@extends('layouts.app')

@section('title', $materi->jenis === 'quiz' ? 'Konfigurasi Kuis: ' . $materi->judul : 'Edit Materi: ' . $materi->judul)

@section('content')

{{-- TOP NAV --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}"
       class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pelatihan
    </a>
    @if($materi->jenis === 'quiz')
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.soal.import', $materi->id) }}"
           class="inline-flex items-center gap-2 bg-white border border-border hover:bg-secondary text-text-primary font-semibold px-4 py-2 rounded-xl text-sm shadow-sm transition-all">
            <i data-lucide="upload-cloud" class="w-4 h-4 text-text-secondary"></i> Import Soal
        </a>
        <a href="{{ route('admin.kuis.peserta', $materi->id) }}"
           class="inline-flex items-center gap-2 bg-accent hover:bg-accent-hover text-white font-bold px-4 py-2 rounded-xl text-sm shadow-sm transition-all">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Nilai Peserta
        </a>
    </div>
    @endif
</div>

@if($materi->jenis === 'quiz')

{{-- FLASH MESSAGES --}}
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm flex gap-3 items-start">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <ul class="list-disc pl-4 space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-success px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-3">
        <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i>
        <p class="font-semibold">{{ session('success') }}</p>
    </div>
@endif

<form id="quiz-config-form" action="{{ route('admin.materi.update', $materi->id) }}" method="POST">
    @csrf @method('PUT')
    <input type="hidden" name="jenis" value="quiz">
    <input type="hidden" name="urutan" value="{{ $materi->urutan }}">
    <input type="hidden" name="durasi_baca" value="{{ $materi->durasi_baca ?? 0 }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== KIRI (2/3) ===== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- CARD: Informasi Dasar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#EFF6FF">
                        <i data-lucide="file-text" class="w-4 h-4 text-primary"></i>
                    </div>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Informasi Dasar</p>
                        <p class="text-xs text-text-secondary">Nama dan instruksi kuis untuk peserta</p>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-text-primary mb-1.5">
                            Judul Kuis <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="judul" name="judul"
                            value="{{ old('judul', $materi->judul) }}" required
                            placeholder="Contoh: Evaluasi Akhir Modul 1"
                            class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm transition-all">
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-text-primary mb-1.5">
                            Instruksi untuk Peserta
                            <span class="text-xs font-normal text-text-secondary">(opsional)</span>
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            placeholder="Contoh: Kerjakan soal dengan jujur dan mandiri..."
                            class="w-full px-4 py-3 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-none text-sm transition-all">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- CARD: Penilaian & Waktu --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#FEF9EC">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-accent"></i>
                    </div>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Penilaian & Waktu</p>
                        <p class="text-xs text-text-secondary">Ambang lulus, durasi pengerjaan, dan batas percobaan</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                        {{-- Nilai Lulus --}}
                        <div class="rounded-xl p-4 text-center" style="background:#FEF9EC;border:1px solid #F0E4B0">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#C5A02E22">
                                <i data-lucide="target" class="w-4 h-4 text-accent"></i>
                            </div>
                            <label for="passing_grade" class="block text-xs font-bold text-accent uppercase tracking-widest mb-2">Nilai Lulus</label>
                            <div class="relative">
                                <input type="number" id="passing_grade" name="passing_grade"
                                    value="{{ old('passing_grade', $materi->passing_grade ?? 70) }}"
                                    min="0" max="100" required
                                    class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center text-accent focus:ring-2 focus:ring-accent">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-accent font-bold text-sm">%</span>
                            </div>
                            <p class="text-xs text-text-secondary mt-2">Rentang 0 – 100</p>
                        </div>

                        {{-- Durasi --}}
                        <div class="rounded-xl p-4 text-center bg-secondary border border-border">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#EFF6FF">
                                <i data-lucide="clock" class="w-4 h-4 text-primary"></i>
                            </div>
                            <label for="durasi_menit" class="block text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">Durasi</label>
                            <input type="number" id="durasi_menit" name="durasi_menit"
                                value="{{ old('durasi_menit', $materi->durasi_menit ?? 30) }}"
                                min="0" required
                                class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center focus:ring-2 focus:ring-primary">
                            <p class="text-xs text-text-secondary mt-2">Menit &bull; 0 = Tanpa batas</p>
                        </div>

                        {{-- Maks Percobaan --}}
                        <div class="rounded-xl p-4 text-center bg-secondary border border-border">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#FFF7ED">
                                <i data-lucide="refresh-ccw" class="w-4 h-4" style="color:#F97316"></i>
                            </div>
                            <label for="max_attempts" class="block text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">Maks. Coba</label>
                            <input type="number" id="max_attempts" name="max_attempts"
                                value="{{ old('max_attempts', $materi->max_attempts ?? 3) }}"
                                min="0" required
                                class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center focus:ring-2" style="focus:ring-color:#F97316">
                            <p class="text-xs text-text-secondary mt-2">0 = Tidak terbatas</p>
                        </div>

                        {{-- Poin Penyelesaian --}}
                        <div class="rounded-xl p-4 text-center bg-secondary border border-border">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto mb-3" style="background:#F3E8FF">
                                <i data-lucide="award" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <label for="poin" class="block text-xs font-bold text-text-secondary uppercase tracking-widest mb-2">Poin</label>
                            <input type="number" id="poin" name="poin"
                                value="{{ old('poin', $materi->poin ?? 50) }}"
                                min="0" required
                                class="w-full px-2 py-2 border border-border rounded-lg outline-none bg-white text-xl font-bold text-center focus:ring-2 focus:ring-purple-500">
                            <p class="text-xs text-text-secondary mt-2">Reward Poin</p>
                        </div>

                    </div>
                </div>
            </div>

            {{-- CARD: Perilaku Soal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#F5F3FF">
                        <i data-lucide="shuffle" class="w-4 h-4" style="color:#7C3AED"></i>
                    </div>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Perilaku Soal</p>
                        <p class="text-xs text-text-secondary">Konfigurasi tampilan saat peserta mengerjakan</p>
                    </div>
                </div>
                <div class="divide-y divide-border">

                    {{-- Acak Soal --}}
                    <label class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-secondary transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#EFF6FF">
                                <i data-lucide="shuffle" class="w-4 h-4 text-primary"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-text-primary">Acak Urutan Soal</p>
                                <p class="text-xs text-text-secondary mt-0.5">Pertanyaan ditampilkan acak setiap kali mengerjakan</p>
                            </div>
                        </div>
                        <input type="checkbox" name="acak_soal" value="1"
                            {{ old('acak_soal', $materi->acak_soal) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-accent border-border focus:ring-accent cursor-pointer shrink-0">
                    </label>

                    {{-- Acak Jawaban --}}
                    <label class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-secondary transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#F5F3FF">
                                <i data-lucide="list-ordered" class="w-4 h-4" style="color:#7C3AED"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-text-primary">Acak Pilihan Jawaban</p>
                                <p class="text-xs text-text-secondary mt-0.5">Urutan A, B, C, D diacak (Multiple Choice & Select)</p>
                            </div>
                        </div>
                        <input type="checkbox" name="acak_jawaban" value="1"
                            {{ old('acak_jawaban', $materi->acak_jawaban) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-accent border-border focus:ring-accent cursor-pointer shrink-0">
                    </label>

                    {{-- Tampilkan Feedback --}}
                    <label class="flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-secondary transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#ECFDF5">
                                <i data-lucide="eye" class="w-4 h-4 text-success"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-text-primary">Tampilkan Feedback & Kunci Jawaban</p>
                                <p class="text-xs text-text-secondary mt-0.5">Peserta bisa melihat jawaban benar setelah selesai</p>
                            </div>
                        </div>
                        <input type="checkbox" name="tampilkan_feedback" value="1"
                            {{ old('tampilkan_feedback', $materi->tampilkan_feedback) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-accent border-border focus:ring-accent cursor-pointer shrink-0">
                    </label>

                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex items-center justify-between gap-3 pb-2">
                <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}"
                   class="px-5 py-2.5 border border-border rounded-xl text-text-secondary hover:bg-secondary font-medium text-sm transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="bg-accent hover:bg-accent-hover text-white font-bold py-2.5 px-8 rounded-xl shadow-sm flex items-center gap-2 text-sm transition-all">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Konfigurasi
                </button>
            </div>
        </div>

        {{-- ===== KANAN / SIDEBAR (1/3) ===== --}}
        <div class="space-y-5">

            {{-- Sidebar: Status Publish --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border p-5">
                <p class="font-bold text-text-primary text-sm mb-3 flex items-center gap-2">
                    <i data-lucide="globe" class="w-4 h-4 text-text-secondary"></i> Status Publikasi
                </p>
                <label class="flex items-center justify-between gap-3 cursor-pointer p-3 rounded-xl border border-border hover:bg-secondary transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-text-primary">Kuis Aktif (Published)</p>
                        <p class="text-xs text-text-secondary mt-0.5">
                            {{ $materi->is_active ? 'Dapat diakses peserta' : 'Belum dipublikasikan' }}
                        </p>
                    </div>
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $materi->is_active) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-success border-border focus:ring-success cursor-pointer shrink-0">
                </label>
            </div>

            {{-- Sidebar: Anti-Cheat --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
                <div class="px-5 py-4 border-b border-border flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:#FEF2F2">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-danger"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-text-primary text-sm">Anti-Cheat Proctoring</p>
                        <p class="text-xs text-text-secondary">Sistem pengawasan otomatis</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $materi->strict_anti_cheat ? 'bg-danger text-white' : 'bg-secondary text-text-secondary' }}">
                        {{ $materi->strict_anti_cheat ? 'AKTIF' : 'OFF' }}
                    </span>
                </div>
                <div class="p-5 space-y-3">
                    {{-- Toggle --}}
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-text-primary">Aktifkan Proctoring</p>
                            <p class="text-xs text-text-secondary mt-0.5">Pantau kejujuran peserta</p>
                        </div>
                        <input type="checkbox" name="strict_anti_cheat" value="1"
                            {{ old('strict_anti_cheat', $materi->strict_anti_cheat) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-danger border-border cursor-pointer shrink-0" style="accent-color:#C0392B">
                    </label>

                    {{-- Quick info --}}
                    <div class="rounded-xl p-3 space-y-1.5" style="background:#FEF2F2">
                        <p class="text-xs font-bold text-danger mb-2">Apa yang dideteksi:</p>
                        <p class="text-xs text-text-secondary flex items-center gap-1.5">
                            <i data-lucide="eye-off" class="w-3 h-3 text-danger shrink-0"></i>
                            Berpindah tab / jendela
                        </p>
                        <p class="text-xs text-text-secondary flex items-center gap-1.5">
                            <i data-lucide="monitor-off" class="w-3 h-3 text-danger shrink-0"></i>
                            Alt+Tab / browser blur
                        </p>
                        <p class="text-xs text-text-secondary flex items-center gap-1.5">
                            <i data-lucide="triangle-alert" class="w-3 h-3 shrink-0" style="color:#F97316"></i>
                            Auto-submit setelah 3× pelanggaran
                        </p>
                    </div>

                    {{-- Button Detail --}}
                    <button type="button" onclick="openAntiCheatModal()"
                        class="w-full text-sm font-semibold text-danger border border-danger rounded-xl py-2.5 hover:bg-red-50 transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="info" class="w-4 h-4"></i> Detail Sistem Proctoring
                    </button>
                </div>
            </div>

            {{-- Sidebar: Ringkasan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-border p-5">
                <p class="font-bold text-text-primary text-sm mb-3 flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 text-primary"></i> Ringkasan
                </p>
                <div class="space-y-2 text-sm divide-y divide-border">
                    <div class="flex justify-between py-2">
                        <span class="text-text-secondary text-xs">Total Soal</span>
                        <span class="font-bold text-text-primary">{{ $materi->soals->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-text-secondary text-xs">Total Poin</span>
                        <span class="font-bold text-text-primary">{{ $materi->soals->sum('bobot') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-text-secondary text-xs">Nilai Lulus</span>
                        <span class="font-bold text-accent">{{ $materi->passing_grade ?? 70 }}%</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-text-secondary text-xs">Durasi</span>
                        <span class="font-bold text-text-primary text-xs">{{ $materi->durasi_menit ? $materi->durasi_menit.' mnt' : '∞' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-text-secondary text-xs">Maks. Percobaan</span>
                        <span class="font-bold text-text-primary text-xs">{{ $materi->max_attempts ?: '∞' }}x</span>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Komposisi Soal --}}
            @if($materi->soals->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-border p-5">
                <p class="font-bold text-text-primary text-sm mb-3">Komposisi Jenis Soal</p>
                <div class="space-y-2">
                    @foreach([['pilihan_ganda','Multiple Choice','blue'],['multi_select','Multiple Select','violet'],['essay','Free Text','green'],['isian_singkat','Fill in Blank','yellow'],['menjodohkan','Matching','orange']] as [$k,$n,$c])
                        @php $cnt = $materi->soals->where('tipe', $k)->count(); @endphp
                        @if($cnt > 0)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-text-secondary">{{ $n }}</span>
                            <span class="font-bold px-2.5 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ $cnt }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</form>

{{-- CARD DAFTAR SOAL (full width) --}}
<div class="mt-6 bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
    <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-secondary">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#EFF6FF">
                <i data-lucide="list-checks" class="w-5 h-5 text-primary"></i>
            </div>
            <div>
                <p class="font-bold text-text-primary">Daftar Soal</p>
                <p class="text-xs text-text-secondary">
                    <strong>{{ $materi->soals->count() }}</strong> soal &bull;
                    Total poin: <strong>{{ $materi->soals->sum('bobot') }}</strong>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.materi.soal.create', $materi->id) }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Soal
        </a>
    </div>

    @php
        $tipeLabels = [
            'pilihan_ganda' => ['label'=>'Multiple Choice','color'=>'bg-blue-100 text-blue-700'],
            'multi_select'  => ['label'=>'Multiple Select','color'=>'bg-violet-100 text-violet-700'],
            'essay'         => ['label'=>'Free Text','color'=>'bg-green-100 text-green-700'],
            'isian_singkat' => ['label'=>'Fill in Blank','color'=>'bg-yellow-100 text-yellow-700'],
            'menjodohkan'   => ['label'=>'Matching','color'=>'bg-orange-100 text-orange-700'],
        ];
    @endphp

    @if($materi->soals->isEmpty())
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-secondary rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="help-circle" class="w-8 h-8 text-border"></i>
            </div>
            <p class="font-bold text-text-primary">Belum ada soal</p>
            <p class="text-sm text-text-secondary mt-1 mb-5">Klik tombol di bawah untuk mulai menambahkan pertanyaan.</p>
            <a href="{{ route('admin.materi.soal.create', $materi->id) }}"
               class="inline-flex items-center gap-2 bg-primary text-white hover:bg-primary-hover font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Soal Pertama
            </a>
        </div>
    @else
        <div class="divide-y divide-border">
            @foreach($materi->soals as $idx => $soal)
                @php $t = $tipeLabels[$soal->tipe] ?? ['label'=>$soal->tipe,'color'=>'bg-secondary text-text-secondary']; @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-secondary transition-colors group">
                    <span class="w-7 h-7 rounded-full bg-secondary flex items-center justify-center text-xs font-bold text-text-secondary shrink-0">{{ $idx+1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-text-primary font-medium line-clamp-1">{{ $soal->pertanyaan }}</p>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $t['color'] }}">{{ $t['label'] }}</span>
                            <span class="text-xs text-text-secondary">{{ $soal->bobot }} poin</span>
                            @if(!$soal->is_active)<span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-secondary text-text-secondary">Draft</span>@endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.soal.edit', $soal->id) }}"
                           class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 rounded-lg transition-colors">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus soal ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-text-secondary hover:text-danger hover:bg-red-50 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-6 py-3 bg-secondary border-t border-border">
            <a href="{{ route('admin.materi.soal.create', $materi->id) }}"
               class="inline-flex items-center gap-1.5 text-sm text-primary hover:text-primary-hover font-medium transition-colors">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Soal Lagi
            </a>
        </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- MODAL: Detail Anti-Cheat                                     --}}
{{-- Menggunakan inline style agar tidak bergantung Tailwind JIT  --}}
{{-- ============================================================ --}}
<div id="anti-cheat-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    {{-- Backdrop --}}
    <div onclick="closeAntiCheatModal()"
         style="position:absolute;inset:0;background:rgba(0,0,0,0.55);cursor:pointer;"></div>

    {{-- Panel --}}
    <div style="position:relative;background:#fff;border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.2);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;z-index:1;">

        {{-- Header --}}
        <div style="position:sticky;top:0;background:#fff;border-bottom:1px solid #E2E6EC;padding:1rem 1.5rem;display:flex;align-items:center;gap:0.75rem;border-radius:1rem 1rem 0 0;">
            <div style="width:2.5rem;height:2.5rem;background:#FEF2F2;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-lucide="shield-alert" style="width:1.25rem;height:1.25rem;color:#C0392B;"></i>
            </div>
            <div style="flex:1">
                <p style="font-weight:700;color:#1A1A1A;font-size:0.9375rem;">Sistem Anti-Cheat Proctoring</p>
                <p style="font-size:0.75rem;color:#5C6470;margin-top:0.125rem;">Cara kerja pengawasan integritas kuis</p>
            </div>
            <button onclick="closeAntiCheatModal()"
                style="width:2rem;height:2rem;display:flex;align-items:center;justify-content:center;border-radius:0.5rem;border:none;background:transparent;cursor:pointer;color:#5C6470;"
                onmouseover="this.style.background='#F5F7FA'" onmouseout="this.style.background='transparent'">
                <i data-lucide="x" style="width:1rem;height:1rem;"></i>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Intro --}}
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:0.75rem;padding:1rem;">
                <p style="font-size:0.875rem;color:#1A1A1A;line-height:1.6;">
                    Sistem proctoring bekerja <strong>secara otomatis di browser peserta</strong> tanpa software tambahan.
                    Saat aktif, sistem <strong style="color:#C0392B;">memantau perilaku peserta</strong> sepanjang pengerjaan kuis.
                </p>
            </div>

            {{-- Yang Dideteksi --}}
            <div>
                <p style="font-size:0.6875rem;font-weight:700;color:#5C6470;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.75rem;">Yang Dideteksi Sistem</p>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">

                    <div style="display:flex;align-items:flex-start;gap:0.75rem;background:#FEF2F2;border:1px solid #FECACA;border-radius:0.75rem;padding:0.875rem;">
                        <div style="width:2rem;height:2rem;background:#FEE2E2;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.125rem;">
                            <i data-lucide="layers" style="width:1rem;height:1rem;color:#C0392B;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#C0392B;">Perpindahan Tab / Jendela Browser</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;line-height:1.5;">
                                Terdeteksi saat peserta berpindah ke tab lain (Google, ChatGPT, dll).
                                Menggunakan <code style="background:#FEE2E2;padding:0 4px;border-radius:3px;font-size:0.7rem;color:#C0392B;">visibilitychange</code> API.
                            </p>
                        </div>
                    </div>

                    <div style="display:flex;align-items:flex-start;gap:0.75rem;background:#FEF2F2;border:1px solid #FECACA;border-radius:0.75rem;padding:0.875rem;">
                        <div style="width:2rem;height:2rem;background:#FEE2E2;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.125rem;">
                            <i data-lucide="monitor-x" style="width:1rem;height:1rem;color:#C0392B;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#C0392B;">Kehilangan Fokus Browser</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;line-height:1.5;">
                                Terdeteksi saat peserta menekan <kbd style="background:#FEE2E2;padding:0 4px;border-radius:3px;font-size:0.7rem;">Alt+Tab</kbd>,
                                minimize, atau klik taskbar. Menggunakan event <code style="background:#FEE2E2;padding:0 4px;border-radius:3px;font-size:0.7rem;color:#C0392B;">window.blur</code>.
                            </p>
                        </div>
                    </div>

                    <div style="display:flex;align-items:flex-start;gap:0.75rem;background:#FFF7ED;border:1px solid #FED7AA;border-radius:0.75rem;padding:0.875rem;">
                        <div style="width:2rem;height:2rem;background:#FFEDD5;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.125rem;">
                            <i data-lucide="minimize-2" style="width:1rem;height:1rem;color:#F97316;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#EA580C;">Minimize / Background</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;line-height:1.5;">
                                Terdeteksi saat peserta meminimize browser atau halaman masuk ke background karena notifikasi.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Mekanisme Sanksi --}}
            <div>
                <p style="font-size:0.6875rem;font-weight:700;color:#5C6470;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.75rem;">Mekanisme Peringatan & Sanksi</p>
                <div style="display:flex;flex-direction:column;gap:0;">
                    {{-- Step 1 --}}
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #E2E6EC;">
                        <div style="width:1.75rem;height:1.75rem;background:#FEF9C3;border:2px solid #EAB308;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#CA8A04;flex-shrink:0;margin-top:0.125rem;">1</div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#1A1A1A;">Pelanggaran ke-1</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">Popup peringatan muncul. Peserta masih dapat melanjutkan kuis.</p>
                        </div>
                    </div>
                    {{-- Step 2 --}}
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #E2E6EC;">
                        <div style="width:1.75rem;height:1.75rem;background:#FFEDD5;border:2px solid #F97316;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#EA580C;flex-shrink:0;margin-top:0.125rem;">2</div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#1A1A1A;">Pelanggaran ke-2</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">Peringatan keras: satu pelanggaran lagi = kuis dikumpulkan otomatis.</p>
                        </div>
                    </div>
                    {{-- Step 3 --}}
                    <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 0;">
                        <div style="width:1.75rem;height:1.75rem;background:#C0392B;border:2px solid #C0392B;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0;margin-top:0.125rem;">3</div>
                        <div>
                            <p style="font-size:0.875rem;font-weight:700;color:#C0392B;">Pelanggaran ke-3 → Auto Submit</p>
                            <p style="font-size:0.75rem;color:#5C6470;margin-top:0.25rem;">Kuis dikumpulkan otomatis dengan jawaban yang sudah terisi. Percobaan dianggap selesai.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterbatasan --}}
            <div style="background:#F5F7FA;border:1px solid #E2E6EC;border-radius:0.75rem;padding:1rem;">
                <p style="font-size:0.6875rem;font-weight:700;color:#5C6470;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.625rem;">Yang Tidak Terdeteksi</p>
                <div style="display:flex;flex-direction:column;gap:0.375rem;">
                    <p style="font-size:0.75rem;color:#5C6470;">— Membaca buku / modul cetak</p>
                    <p style="font-size:0.75rem;color:#5C6470;">— Menggunakan HP / perangkat lain secara bersamaan</p>
                    <p style="font-size:0.75rem;color:#5C6470;">— Diskusi dengan orang di sekitar</p>
                </div>
                <p style="font-size:0.6875rem;color:#5C6470;margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid #E2E6EC;line-height:1.5;">
                    Sistem ini adalah pencegahan berbasis browser. Untuk ujian bernilai tinggi, pertimbangkan pengawas manual tambahan.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div style="position:sticky;bottom:0;background:#fff;border-top:1px solid #E2E6EC;padding:1rem 1.5rem;display:flex;justify-content:flex-end;border-radius:0 0 1rem 1rem;">
            <button onclick="closeAntiCheatModal()"
                style="padding:0.5rem 1.5rem;background:#F5F7FA;border:1px solid #E2E6EC;border-radius:0.75rem;font-weight:600;font-size:0.875rem;color:#1A1A1A;cursor:pointer;transition:background 0.15s;"
                onmouseover="this.style.background='#E2E6EC'" onmouseout="this.style.background='#F5F7FA'">
                Mengerti
            </button>
        </div>
    </div>
</div>

@else
{{-- ================================================================ --}}
{{-- NON-QUIZ MATERI EDIT                                             --}}
{{-- ================================================================ --}}
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
        <div class="px-6 py-5 border-b border-border flex items-center gap-4" style="background:linear-gradient(to right,#EFF6FF,transparent)">
            @php
                $icon = match($materi->jenis) {
                    'pdf'         => 'file-text',
                    'ppt','pptx'  => 'presentation',
                    'video_embed' => 'play-circle',
                    'link'        => 'link-2',
                    default       => 'file'
                };
            @endphp
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-primary shrink-0" style="background:#EFF6FF">
                <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-text-primary">Edit Materi</h2>
                <p class="text-xs text-text-secondary mt-0.5 line-clamp-1">{{ $materi->judul }}</p>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-5">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-xl text-sm flex gap-3 items-start">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-success px-4 py-3 rounded-xl text-sm flex items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i>
                    <p class="font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                {{-- Informasi Dasar --}}
                <div class="rounded-xl border border-border overflow-hidden">
                    <div class="px-5 py-3 bg-secondary border-b border-border flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-primary"></i>
                        <p class="text-sm font-bold text-text-primary">Informasi Dasar</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label for="judul" class="block text-sm font-semibold text-text-primary mb-1.5">Judul Materi <span class="text-danger">*</span></label>
                            <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" required
                                class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm transition-all">
                        </div>
                        <div>
                            <label for="deskripsi" class="block text-sm font-semibold text-text-primary mb-1.5">Deskripsi <span class="text-xs font-normal text-text-secondary">(opsional)</span></label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"
                                class="w-full px-4 py-3 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-none text-sm transition-all">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Konten --}}
                <div class="rounded-xl border border-border overflow-hidden">
                    <div class="px-5 py-3 bg-secondary border-b border-border flex items-center gap-2">
                        <i data-lucide="settings-2" class="w-4 h-4 text-accent"></i>
                        <p class="text-sm font-bold text-text-primary">Pengaturan Konten</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label for="jenis" class="block text-sm font-semibold text-text-primary mb-1.5">Jenis <span class="text-danger">*</span></label>
                                <select id="jenis" name="jenis" required onchange="toggleContentInput(this.value)"
                                    class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white text-sm transition-all">
                                    <option value="pdf"         {{ old('jenis',$materi->jenis)=='pdf'         ?'selected':'' }}>📄 PDF</option>
                                    <option value="ppt"         {{ old('jenis',$materi->jenis)=='ppt'         ?'selected':'' }}>📊 PPT</option>
                                    <option value="pptx"        {{ old('jenis',$materi->jenis)=='pptx'        ?'selected':'' }}>📊 PPTX</option>
                                    <option value="video_embed" {{ old('jenis',$materi->jenis)=='video_embed' ?'selected':'' }}>▶️ YouTube</option>
                                    <option value="link"        {{ old('jenis',$materi->jenis)=='link'        ?'selected':'' }}>🔗 Link Eksternal</option>
                                </select>
                            </div>
                            <div>
                                <label for="urutan" class="block text-sm font-semibold text-text-primary mb-1.5">Urutan <span class="text-danger">*</span></label>
                                <input type="number" id="urutan" name="urutan" value="{{ old('urutan',$materi->urutan) }}" required min="1"
                                    class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm font-bold text-center transition-all">
                            </div>
                            <div>
                                <label for="durasi_baca" class="block text-sm font-semibold text-text-primary mb-1.5">Estimasi Waktu Baca (Mnt) <span class="text-danger">*</span></label>
                                <input type="number" id="durasi_baca" name="durasi_baca" value="{{ old('durasi_baca',$materi->durasi_baca) }}" required min="1"
                                    class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm font-bold text-center transition-all">
                            </div>
                            <div>
                                <label for="poin" class="block text-sm font-semibold text-text-primary mb-1.5">Poin Penyelesaian <span class="text-danger">*</span></label>
                                <input type="number" id="poin" name="poin" value="{{ old('poin', $materi->poin ?? 50) }}" required min="0"
                                    class="w-full px-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm font-bold text-center transition-all">
                            </div>
                        </div>

                        <div id="file_input_container" class="{{ in_array(old('jenis',$materi->jenis),['link','video_embed']) ? 'hidden' : '' }}">
                            <label class="block text-sm font-semibold text-text-primary mb-1.5">
                                Ganti File <span class="text-xs font-normal text-text-secondary">(kosongkan jika tidak berubah)</span>
                            </label>
                            <div class="border-2 border-dashed border-border rounded-xl p-4 text-center hover:border-primary transition-colors bg-secondary">
                                <input type="file" name="file_upload" accept=".pdf,.ppt,.pptx"
                                    class="w-full text-sm text-text-secondary file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-primary file:font-semibold cursor-pointer">
                                <p class="text-xs text-text-secondary mt-2">Maks. 10MB · PDF, PPT, PPTX</p>
                                @if($materi->file_path)
                                    <p class="text-xs text-success font-medium mt-1.5 flex items-center justify-center gap-1">
                                        <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i> File saat ini tersedia
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div id="url_input_container" class="{{ in_array(old('jenis',$materi->jenis),['link','video_embed']) ? '' : 'hidden' }}">
                            <label for="url_link" class="block text-sm font-semibold text-text-primary mb-1.5">URL / Tautan <span class="text-danger">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none">
                                    <i data-lucide="link-2" class="w-4 h-4"></i>
                                </span>
                                <input type="url" id="url_link" name="url_link" value="{{ old('url_link',$materi->url_link) }}" placeholder="https://..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status & Tombol --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active',$materi->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-success border-border focus:ring-success cursor-pointer">
                        <div>
                            <p class="text-sm font-semibold text-text-primary">Materi Aktif (Publish)</p>
                            <p class="text-xs text-text-secondary">Dapat diakses oleh peserta pelatihan</p>
                        </div>
                    </label>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}"
                           class="px-5 py-2.5 border border-border rounded-xl text-text-secondary hover:bg-secondary font-medium text-sm transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-7 rounded-xl shadow-sm flex items-center gap-2 text-sm transition-all">
                            <i data-lucide="save" class="w-4 h-4"></i> Perbarui Materi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Non-quiz: toggle file/url
function toggleContentInput(jenis) {
    const f = document.getElementById('file_input_container');
    const u = document.getElementById('url_input_container');
    if (!f || !u) return;
    if (jenis === 'link' || jenis === 'video_embed') {
        f.classList.add('hidden'); u.classList.remove('hidden');
    } else {
        u.classList.add('hidden'); f.classList.remove('hidden');
    }
}

// Anti-Cheat Modal
function openAntiCheatModal() {
    const m = document.getElementById('anti-cheat-modal');
    if (!m) return;
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAntiCheatModal() {
    const m = document.getElementById('anti-cheat-modal');
    if (!m) return;
    m.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAntiCheatModal(); });
</script>
@endpush
