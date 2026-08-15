@extends('layouts.app')

@section('title', $materi->jenis === 'quiz' ? 'Konfigurasi Kuis: ' . $materi->judul : 'Edit Materi: ' . $materi->judul)

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pelatihan
    </a>
    @if($materi->jenis === 'quiz')
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.soal.import', $materi->id) }}"
           class="inline-flex items-center gap-2 bg-secondary border border-border hover:bg-secondary/70 text-text-primary font-bold px-4 py-2 rounded-xl transition-all text-sm">
            <i data-lucide="upload-cloud" class="w-4 h-4"></i> Import Soal
        </a>
        <a href="{{ route('admin.kuis.peserta', $materi->id) }}"
           class="inline-flex items-center gap-2 bg-accent hover:bg-accent-hover text-white font-bold px-4 py-2 rounded-xl transition-all shadow-sm text-sm">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Nilai Peserta
        </a>
    </div>
    @endif
</div>

@if($materi->jenis === 'quiz')
{{-- =============================================================== --}}
{{-- QUIZ CONFIGURATION VIEW --}}
{{-- =============================================================== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Settings + Question List --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- SECTION 1: Basic Info & Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
            <div class="px-6 py-5 border-b border-border flex items-start gap-4">
                <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center text-accent shrink-0 mt-0.5">
                    <i data-lucide="settings-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-text-primary">Konfigurasi Kuis</h2>
                    <p class="text-sm text-text-secondary mt-1">Atur nama, durasi, dan aturan pengerjaan untuk kuis ini.</p>
                </div>
            </div>

            <div class="p-6 md:p-8">
                @if($errors->any())
                    <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm flex gap-3 items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-lg mb-6 text-sm flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i> 
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    {{-- Hidden fields to pass unchanged values --}}
                    <input type="hidden" name="jenis" value="quiz">
                    <input type="hidden" name="urutan" value="{{ $materi->urutan }}">
                    <input type="hidden" name="durasi_baca" value="{{ $materi->durasi_baca }}">
                    @if($materi->is_active) <input type="hidden" name="is_active" value="1"> @endif

                    <div class="bg-secondary/20 rounded-xl border border-border/50 p-6">
                        <h3 class="text-lg font-bold text-text-primary mb-5 flex items-center gap-2">
                            <i data-lucide="file-text" class="w-5 h-5 text-primary"></i> 1. Informasi Dasar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-7">
                            <div class="md:col-span-2">
                                <label for="judul" class="block text-sm font-semibold text-text-primary mb-1.5">Nama Kuis <span class="text-danger">*</span></label>
                                <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" required
                                    class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all">
                            </div>

                            <div class="md:col-span-2">
                                <label for="deskripsi" class="block text-sm font-semibold text-text-primary mb-1.5">Deskripsi / Instruksi</label>
                                <textarea id="deskripsi" name="deskripsi" rows="3"
                                    class="w-full px-4 py-3 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none resize-none bg-white transition-all">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-secondary/20 rounded-xl border border-border/50 p-6">
                        <h3 class="text-lg font-bold text-text-primary mb-5 flex items-center gap-2">
                            <i data-lucide="settings-2" class="w-5 h-5 text-accent"></i> 2. Pengaturan & Penilaian
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pl-0 md:pl-7">
                            <div>
                                <label for="passing_grade" class="block text-sm font-semibold text-text-primary mb-1.5">Nilai Lulus (0–100) <span class="text-danger">*</span></label>
                                <div class="relative">
                                    <input type="number" id="passing_grade" name="passing_grade" value="{{ old('passing_grade', $materi->passing_grade ?? 70) }}" min="0" max="100" required
                                        class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all pr-10 text-lg font-bold text-center">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary font-bold">%</span>
                                </div>
                            </div>

                            <div>
                                <label for="durasi_menit" class="block text-sm font-semibold text-text-primary mb-1.5">Waktu (Menit) <span class="text-danger">*</span></label>
                                <input type="number" id="durasi_menit" name="durasi_menit" value="{{ old('durasi_menit', $materi->durasi_menit ?? 30) }}" min="0" required
                                    class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all text-lg font-bold text-center">
                                <p class="text-[11px] font-medium text-text-secondary mt-1.5 flex items-center gap-1"><i data-lucide="info" class="w-3 h-3"></i> Isi 0 = Tanpa batas waktu</p>
                            </div>

                            <div>
                                <label for="max_attempts" class="block text-sm font-semibold text-text-primary mb-1.5">Maks. Percobaan <span class="text-danger">*</span></label>
                                <input type="number" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', $materi->max_attempts ?? 3) }}" min="0" required
                                    class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all text-lg font-bold text-center">
                                <p class="text-[11px] font-medium text-text-secondary mt-1.5 flex items-center gap-1"><i data-lucide="info" class="w-3 h-3"></i> Isi 0 = Tidak terbatas</p>
                            </div>

                            <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                                <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-accent hover:shadow-sm transition-all">
                                    <input type="checkbox" name="acak_soal" value="1" {{ old('acak_soal', $materi->acak_soal) ? 'checked' : '' }} class="mt-1 w-5 h-5 text-accent rounded border-border focus:ring-accent transition-colors">
                                    <div>
                                        <p class="text-sm font-bold text-text-primary group-hover:text-accent transition-colors">Acak Urutan Soal</p>
                                        <p class="text-xs text-text-secondary mt-1 leading-relaxed">Pertanyaan akan diacak setiap kali peserta mengerjakan.</p>
                                    </div>
                                </label>
                                
                                <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-accent hover:shadow-sm transition-all">
                                    <input type="checkbox" name="acak_jawaban" value="1" {{ old('acak_jawaban', $materi->acak_jawaban) ? 'checked' : '' }} class="mt-1 w-5 h-5 text-accent rounded border-border focus:ring-accent transition-colors">
                                    <div>
                                        <p class="text-sm font-bold text-text-primary group-hover:text-accent transition-colors">Acak Pilihan Jawaban</p>
                                        <p class="text-xs text-text-secondary mt-1 leading-relaxed">Pilihan A, B, C, D akan diacak letaknya (tipe Multiple Choice).</p>
                                    </div>
                                </label>

                                <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-accent hover:shadow-sm transition-all">
                                    <input type="checkbox" name="tampilkan_feedback" value="1" {{ old('tampilkan_feedback', $materi->tampilkan_feedback) ? 'checked' : '' }} class="mt-1 w-5 h-5 text-accent rounded border-border focus:ring-accent transition-colors">
                                    <div>
                                        <p class="text-sm font-bold text-text-primary group-hover:text-accent transition-colors">Tampilkan Feedback & Kunci</p>
                                        <p class="text-xs text-text-secondary mt-1 leading-relaxed">Peserta dapat melihat jawaban benar setelah kuis selesai.</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-border mt-8">
                        <button type="submit" class="bg-accent hover:bg-accent-hover text-white font-bold py-3 px-8 rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                            <i data-lucide="save" class="w-5 h-5"></i> Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SECTION 2: Question List --}}
        <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-secondary/30 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-primary/10 rounded-lg flex items-center justify-center">
                        <i data-lucide="list-checks" class="w-5 h-5 text-primary"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-text-primary">Daftar Soal</h3>
                        <p class="text-xs text-text-secondary">{{ $materi->soals->count() }} soal terdaftar</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.materi.soal.create', $materi->id) }}" class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-hover text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Soal
                    </a>
                </div>
            </div>

            @php
                $tipeLabels = [
                    'pilihan_ganda' => ['label' => 'Multiple Choice', 'color' => 'bg-blue-100 text-blue-700'],
                    'multi_select'  => ['label' => 'Multiple Select',  'color' => 'bg-violet-100 text-violet-700'],
                    'essay'         => ['label' => 'Free Text',         'color' => 'bg-green-100 text-green-700'],
                    'isian_singkat' => ['label' => 'Fill in the Blank', 'color' => 'bg-yellow-100 text-yellow-700'],
                    'menjodohkan'   => ['label' => 'Matching',          'color' => 'bg-orange-100 text-orange-700'],
                ];
            @endphp

            @if($materi->soals->isEmpty())
                <div class="p-12 text-center">
                    <i data-lucide="help-circle" class="w-12 h-12 text-border mx-auto mb-3"></i>
                    <p class="font-medium text-text-primary">Belum ada soal</p>
                    <p class="text-sm text-text-secondary mt-1 mb-5">Klik tombol "Tambah Soal" untuk mulai membuat pertanyaan kuis.</p>
                    <a href="{{ route('admin.materi.soal.create', $materi->id) }}" class="inline-flex items-center gap-2 bg-primary/10 text-primary hover:bg-primary hover:text-white font-medium px-4 py-2 rounded-lg transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Soal Pertama
                    </a>
                </div>
            @else
                <div class="divide-y divide-border">
                    @foreach($materi->soals as $idx => $soal)
                        @php $tipeInfo = $tipeLabels[$soal->tipe] ?? ['label' => $soal->tipe, 'color' => 'bg-secondary text-text-secondary']; @endphp
                        <div class="flex items-start gap-4 px-6 py-4 hover:bg-secondary/20 transition-colors group">
                            <span class="w-7 h-7 rounded-full bg-secondary flex items-center justify-center text-xs font-bold text-text-secondary shrink-0 mt-0.5">{{ $idx + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-text-primary font-medium line-clamp-2">{{ $soal->pertanyaan }}</p>
                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $tipeInfo['color'] }}">{{ $tipeInfo['label'] }}</span>
                                    <span class="text-[11px] text-text-secondary">{{ $soal->bobot }} poin</span>
                                    @if(!$soal->is_active)
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-secondary text-text-secondary">Draft</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                <a href="{{ route('admin.soal.edit', $soal->id) }}" class="p-2 text-text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition-colors tooltip" data-tip="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus soal ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-text-secondary hover:text-danger hover:bg-danger/10 rounded-lg transition-colors tooltip" data-tip="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-3 bg-secondary/30 border-t border-border">
                    <a href="{{ route('admin.materi.soal.create', $materi->id) }}" class="inline-flex items-center gap-1.5 text-sm text-primary hover:text-primary-hover font-medium transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Soal Lagi
                    </a>
                </div>
            @endif
        </div>

    </div>

    {{-- RIGHT: Summary Panel --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-border p-5">
            <h3 class="font-bold text-text-primary mb-4 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-5 h-5 text-primary"></i> Ringkasan
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between py-2 border-b border-border/50">
                    <span class="text-text-secondary">Total Soal</span>
                    <span class="font-bold text-text-primary text-lg">{{ $materi->soals->count() }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-border/50">
                    <span class="text-text-secondary">Total Poin</span>
                    <span class="font-bold text-text-primary">{{ $materi->soals->sum('bobot') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-border/50">
                    <span class="text-text-secondary">Nilai Minimum</span>
                    <span class="font-bold text-accent">{{ $materi->passing_grade ?? 70 }}%</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-border/50">
                    <span class="text-text-secondary">Durasi</span>
                    <span class="font-bold text-text-primary">{{ $materi->durasi_menit ? $materi->durasi_menit . ' mnt' : 'Tanpa batas' }}</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-text-secondary">Maks. Percobaan</span>
                    <span class="font-bold text-text-primary">{{ $materi->max_attempts ?: '∞' }}x</span>
                </div>
            </div>
        </div>

        {{-- Question type breakdown --}}
        @if($materi->soals->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-border p-5">
            <h3 class="font-bold text-text-primary mb-4 text-sm">Komposisi Jenis Soal</h3>
            <div class="space-y-2">
                @foreach([['pilihan_ganda','Multiple Choice','blue'],['multi_select','Multiple Select','violet'],['essay','Free Text','green'],['isian_singkat','Fill in Blank','yellow'],['menjodohkan','Matching','orange']] as [$tipeKey, $tipeNama, $color])
                    @php $count = $materi->soals->where('tipe', $tipeKey)->count(); @endphp
                    @if($count > 0)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text-secondary">{{ $tipeNama }}</span>
                        <span class="font-bold text-{{ $color }}-600 bg-{{ $color }}-50 px-2 py-0.5 rounded-full text-xs">{{ $count }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-border p-5">
            <h3 class="font-bold text-text-primary mb-3 text-sm flex items-center gap-1.5">
                <i data-lucide="settings" class="w-4 h-4"></i> Pengaturan Lainnya
            </h3>
            <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <input type="hidden" name="jenis" value="quiz">
                <input type="hidden" name="judul" value="{{ $materi->judul }}">
                <input type="hidden" name="deskripsi" value="{{ $materi->deskripsi }}">
                <input type="hidden" name="passing_grade" value="{{ $materi->passing_grade }}">
                <input type="hidden" name="durasi_menit" value="{{ $materi->durasi_menit }}">
                <input type="hidden" name="max_attempts" value="{{ $materi->max_attempts }}">
                <input type="hidden" name="urutan" value="{{ $materi->urutan }}">
                <input type="hidden" name="durasi_baca" value="{{ $materi->durasi_baca }}">
                @if($materi->acak_soal) <input type="hidden" name="acak_soal" value="1"> @endif
                @if($materi->acak_jawaban) <input type="hidden" name="acak_jawaban" value="1"> @endif
                @if($materi->tampilkan_feedback) <input type="hidden" name="tampilkan_feedback" value="1"> @endif

                <label class="flex items-center gap-2.5 cursor-pointer group/toggle">
                    <input type="checkbox" name="is_active" value="1" {{ $materi->is_active ? 'checked' : '' }} class="w-4 h-4 text-success rounded border-border focus:ring-success" onchange="this.form.submit()">
                    <div>
                        <p class="text-sm font-medium text-text-primary">Kuis Aktif (Published)</p>
                        <p class="text-xs text-text-secondary">Peserta dapat mengakses kuis ini.</p>
                    </div>
                </label>
            </form>
        </div>
    </div>
</div>

@else
{{-- =============================================================== --}}
{{-- NON-QUIZ MATERI EDIT VIEW --}}
{{-- =============================================================== --}}
<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8 max-w-3xl">
    <div class="mb-6 pb-4 border-b border-border">
        <h2 class="text-xl font-bold text-text-primary">Edit Materi: {{ $materi->judul }}</h2>
    </div>

    @if($errors->any())
        <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="judul" class="block text-sm font-semibold text-text-primary mb-1">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" required
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            </div>

            <div>
                <label for="jenis" class="block text-sm font-semibold text-text-primary mb-1">Jenis Konten <span class="text-danger">*</span></label>
                <select id="jenis" name="jenis" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white" onchange="toggleContentInput(this.value)">
                    <option value="pdf" {{ old('jenis', $materi->jenis) == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                    <option value="ppt" {{ old('jenis', $materi->jenis) == 'ppt' ? 'selected' : '' }}>PowerPoint (PPT)</option>
                    <option value="pptx" {{ old('jenis', $materi->jenis) == 'pptx' ? 'selected' : '' }}>PowerPoint (PPTX)</option>
                    <option value="video_embed" {{ old('jenis', $materi->jenis) == 'video_embed' ? 'selected' : '' }}>Embed Video (YouTube)</option>
                    <option value="link" {{ old('jenis', $materi->jenis) == 'link' ? 'selected' : '' }}>Tautan Eksternal</option>
                </select>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="urutan" class="block text-sm font-semibold text-text-primary mb-1">Urutan <span class="text-danger">*</span></label>
                    <input type="number" id="urutan" name="urutan" value="{{ old('urutan', $materi->urutan) }}" required min="1"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
                <div class="flex-1">
                    <label for="durasi_baca" class="block text-sm font-semibold text-text-primary mb-1">Durasi (Mnt) <span class="text-danger">*</span></label>
                    <input type="number" id="durasi_baca" name="durasi_baca" value="{{ old('durasi_baca', $materi->durasi_baca) }}" required min="1"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
            </div>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-semibold text-text-primary mb-1">Deskripsi / Instruksi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
        </div>

        <div id="file_input_container" class="{{ in_array(old('jenis', $materi->jenis), ['link', 'video_embed']) ? 'hidden' : '' }}">
            <label for="file_upload" class="block text-sm font-semibold text-text-primary mb-1">Unggah Dokumen (Ganti File)</label>
            <input type="file" id="file_upload" name="file_upload" accept=".pdf,.ppt,.pptx"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
            <p class="text-xs text-text-secondary mt-1">Biarkan kosong jika tidak ingin mengubah. Maks 10MB.</p>
            @if($materi->file_path)
                <div class="mt-2 text-sm text-primary font-medium flex items-center gap-1">
                    <i data-lucide="file-check-2" class="w-4 h-4"></i> File saat ini tersedia.
                </div>
            @endif
        </div>

        <div id="url_input_container" class="{{ in_array(old('jenis', $materi->jenis), ['link', 'video_embed']) ? '' : 'hidden' }}">
            <label for="url_link" class="block text-sm font-semibold text-text-primary mb-1">Tautan / URL</label>
            <input type="url" id="url_link" name="url_link" value="{{ old('url_link', $materi->url_link) }}" placeholder="https://..."
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
        </div>

        <div class="flex items-center gap-2 pt-4 border-t border-border">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $materi->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
            <label for="is_active" class="text-sm font-semibold text-text-primary cursor-pointer">Materi Aktif (Publish)</label>
        </div>

        <div class="flex justify-end pt-4 gap-3">
            <a href="{{ route('admin.pelatihan.show', $materi->pelatihan_id) }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                Perbarui Materi
            </button>
        </div>
    </form>
</div>
@endif

@endsection

@push('scripts')
<script>
function toggleContentInput(jenis) {
    const fileContainer = document.getElementById('file_input_container');
    const urlContainer = document.getElementById('url_input_container');
    fileContainer?.classList.add('hidden');
    urlContainer?.classList.add('hidden');
    if (jenis === 'link' || jenis === 'video_embed') {
        urlContainer?.classList.remove('hidden');
    } else {
        fileContainer?.classList.remove('hidden');
    }
}
</script>
@endpush
