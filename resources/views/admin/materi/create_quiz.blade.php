@extends('layouts.app')

@section('title', 'Buat Kuis Baru')

@section('content')

<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1.5 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Kurikulum
    </a>
</div>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8">
        <div class="mb-8 pb-5 border-b border-border flex items-start gap-4">
            <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center text-accent shrink-0 mt-1">
                <i data-lucide="help-circle" class="w-7 h-7"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-text-primary">Buat Kuis Evaluasi</h2>
                <p class="text-sm text-text-secondary mt-1">Anda sedang menambahkan kuis baru ke dalam pelatihan <span class="font-bold text-text-primary">"{{ $pelatihan->judul }}"</span>.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-8 text-sm flex gap-3 items-start">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pelatihan.materi.store', $pelatihan->id) }}" method="POST" class="space-y-8">
            @csrf
            
            <input type="hidden" name="jenis" value="quiz">
            <input type="hidden" name="durasi_baca" value="0">

            {{-- SECTION 1: INFORMASI DASAR --}}
            <div class="bg-secondary/20 rounded-xl border border-border/50 p-6">
                <h3 class="text-lg font-bold text-text-primary mb-5 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-primary"></i> 1. Informasi Dasar
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-7">
                    <div class="md:col-span-2">
                        <label for="judul" class="block text-sm font-semibold text-text-primary mb-1.5">Judul Kuis <span class="text-danger">*</span></label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Evaluasi Akhir Modul 1"
                            class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label for="deskripsi" class="block text-sm font-semibold text-text-primary mb-1.5">Deskripsi / Instruksi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Tuliskan instruksi atau pengantar kuis di sini..."
                            class="w-full px-4 py-3 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none resize-none bg-white transition-all">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div>
                        <label for="urutan" class="block text-sm font-semibold text-text-primary mb-1.5">Urutan pada Kurikulum <span class="text-danger">*</span></label>
                        <div class="relative w-1/2 md:w-3/4">
                            <input type="number" id="urutan" name="urutan" value="{{ old('urutan', $pelatihan->materis->count() + 1) }}" required min="1"
                                class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all text-center font-bold">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: PENGATURAN KUIS --}}
            <div class="bg-secondary/20 rounded-xl border border-border/50 p-6">
                <h3 class="text-lg font-bold text-text-primary mb-5 flex items-center gap-2">
                    <i data-lucide="settings-2" class="w-5 h-5 text-accent"></i> 2. Pengaturan & Penilaian
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pl-7">
                    <div>
                        <label for="passing_grade" class="block text-sm font-semibold text-text-primary mb-1.5">Nilai Lulus (0-100) <span class="text-danger">*</span></label>
                        <div class="relative">
                            <input type="number" id="passing_grade" name="passing_grade" value="{{ old('passing_grade', 70) }}" min="0" max="100" required
                                class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all pr-10 text-lg font-bold text-center">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary font-bold">%</span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="durasi_menit" class="block text-sm font-semibold text-text-primary mb-1.5">Waktu (Menit) <span class="text-danger">*</span></label>
                        <input type="number" id="durasi_menit" name="durasi_menit" value="{{ old('durasi_menit', 30) }}" min="0" required
                            class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all text-lg font-bold text-center">
                        <p class="text-[11px] font-medium text-text-secondary mt-1.5 flex items-center gap-1"><i data-lucide="info" class="w-3 h-3"></i> Isi 0 = Tanpa batas waktu</p>
                    </div>

                    <div>
                        <label for="max_attempts" class="block text-sm font-semibold text-text-primary mb-1.5">Maks. Percobaan <span class="text-danger">*</span></label>
                        <input type="number" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', 3) }}" min="0" required
                            class="w-full px-4 py-2.5 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none bg-white transition-all text-lg font-bold text-center">
                        <p class="text-[11px] font-medium text-text-secondary mt-1.5 flex items-center gap-1"><i data-lucide="info" class="w-3 h-3"></i> Isi 0 = Tidak terbatas</p>
                    </div>

                    <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-accent hover:shadow-sm transition-all">
                            <input type="checkbox" name="acak_soal" value="1" {{ old('acak_soal', true) ? 'checked' : '' }} class="mt-1 w-5 h-5 text-accent rounded border-border focus:ring-accent transition-colors">
                            <div>
                                <p class="text-sm font-bold text-text-primary group-hover:text-accent transition-colors">Acak Urutan Soal</p>
                                <p class="text-xs text-text-secondary mt-1 leading-relaxed">Pertanyaan akan diacak setiap kali peserta mengerjakan.</p>
                            </div>
                        </label>
                        
                        <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-accent hover:shadow-sm transition-all">
                            <input type="checkbox" name="acak_jawaban" value="1" {{ old('acak_jawaban', true) ? 'checked' : '' }} class="mt-1 w-5 h-5 text-accent rounded border-border focus:ring-accent transition-colors">
                            <div>
                                <p class="text-sm font-bold text-text-primary group-hover:text-accent transition-colors">Acak Pilihan Jawaban</p>
                                <p class="text-xs text-text-secondary mt-1 leading-relaxed">Pilihan A, B, C, D akan diacak letaknya (tipe Multiple Choice).</p>
                            </div>
                        </label>

                        <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-accent hover:shadow-sm transition-all">
                            <input type="checkbox" name="tampilkan_feedback" value="1" {{ old('tampilkan_feedback', true) ? 'checked' : '' }} class="mt-1 w-5 h-5 text-accent rounded border-border focus:ring-accent transition-colors">
                            <div>
                                <p class="text-sm font-bold text-text-primary group-hover:text-accent transition-colors">Tampilkan Feedback & Kunci</p>
                                <p class="text-xs text-text-secondary mt-1 leading-relaxed">Peserta dapat melihat jawaban benar setelah kuis selesai.</p>
                            </div>
                        </label>
                        
                        <label class="group flex items-start gap-4 p-4 bg-white border border-border rounded-xl cursor-pointer hover:border-success hover:shadow-sm transition-all">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="mt-1 w-5 h-5 text-success rounded border-border focus:ring-success transition-colors">
                            <div>
                                <p class="text-sm font-bold text-success">Kuis Aktif (Published)</p>
                                <p class="text-xs text-text-secondary mt-1 leading-relaxed">Jika dicentang, kuis akan langsung dapat diakses oleh peserta.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-accent/10 border border-accent/20 rounded-xl p-5 flex items-start gap-4 text-accent-hover">
                <div class="bg-white/50 p-2 rounded-lg shrink-0">
                    <i data-lucide="info" class="w-6 h-6 text-accent"></i>
                </div>
                <p class="text-sm leading-relaxed pt-1">
                    Setelah konfigurasi ini disimpan, Anda akan diarahkan ke halaman pengelolaan di mana Anda bisa <strong>mulai menambahkan daftar soal-soal kuis</strong> (Pilihan Ganda, Essay, Isian Singkat, atau Menjodohkan).
                </p>
            </div>

            <div class="flex justify-end pt-4 border-t border-border gap-4 mt-8">
                <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="px-6 py-3 border border-border rounded-xl text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
                <button type="submit" class="bg-accent hover:bg-accent-hover text-white font-bold py-3 px-8 rounded-xl transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                    Lanjut Buat Soal <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
