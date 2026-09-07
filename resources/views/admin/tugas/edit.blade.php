@extends('layouts.app')

@section('title', 'Edit Tugas: ' . $tugas->judul)

@section('content')
<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Kurikulum
    </a>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-xl overflow-hidden max-w-3xl">
    <div class="px-6 py-4 border-b border-border bg-secondary/30 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-text-primary">Edit Penugasan</h2>
            <p class="text-xs text-text-secondary mt-0.5">Pelatihan: {{ $pelatihan->judul }}</p>
        </div>
        <span class="p-2 rounded-lg bg-primary/10 text-primary">
            <i data-lucide="edit-3" class="w-5 h-5"></i>
        </span>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.tugas.update', $tugas->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <div>
                    <label for="tipe" class="block text-sm font-medium text-text-primary mb-1">Tipe Penugasan <span class="text-danger">*</span></label>
                    <select name="tipe" id="tipe" class="w-full px-4 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" required>
                        <option value="tugas_umum" {{ old('tipe', $tugas->tipe) === 'tugas_umum' ? 'selected' : '' }}>📝 Tugas Biasa / Analisis Kasus (Upload Makalah, Dokumen Hukdis, Laporan)</option>
                        <option value="upload_sertifikat" {{ old('tipe', $tugas->tipe) === 'upload_sertifikat' ? 'selected' : '' }}>🎓 Upload Sertifikat Pembelajaran Eksternal (Diklat Luar, MOOC, Webinar)</option>
                    </select>
                </div>

                <div>
                    <label for="judul" class="block text-sm font-medium text-text-primary mb-1">Judul Penugasan <span class="text-danger">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul', $tugas->judul) }}" class="w-full px-4 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" required>
                    @error('judul') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-text-primary mb-1">Instruksi & Petunjuk Tugas</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="file_lampiran" class="block text-sm font-medium text-text-primary mb-1">File Lampiran / Lembar Soal Kasus (Ganti jika ingin memperbarui)</label>
                    @if($tugas->file_lampiran)
                        <div class="mb-2 p-2 rounded bg-secondary/50 flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-text-secondary">
                                <i data-lucide="paperclip" class="w-4 h-4 text-primary"></i> Lampiran saat ini: 
                                <a href="{{ asset('storage/' . $tugas->file_lampiran) }}" target="_blank" class="text-primary font-medium hover:underline">Download Lampiran</a>
                            </span>
                        </div>
                    @endif
                    <input type="file" name="file_lampiran" id="file_lampiran" class="w-full px-3 py-2 text-sm rounded-lg border border-border file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="text-xs text-text-secondary mt-1">Format: PDF, DOCX, PPTX, ZIP (Maks 10MB).</p>
                    @error('file_lampiran') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-text-primary mb-1">Batas Waktu Pengumpulan (Deadline)</label>
                        <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline', $tugas->deadline ? $tugas->deadline->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors">
                        @error('deadline') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="bobot_nilai" class="block text-sm font-medium text-text-primary mb-1">Skala Nilai Maksimal <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" name="bobot_nilai" id="bobot_nilai" value="{{ old('bobot_nilai', $tugas->bobot_nilai) }}" class="w-full px-4 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" required>
                        @error('bobot_nilai') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="max_file_size_mb" class="block text-sm font-medium text-text-primary mb-1">Maks. Ukuran File Peserta (MB)</label>
                        <input type="number" name="max_file_size_mb" id="max_file_size_mb" value="{{ old('max_file_size_mb', $tugas->max_file_size_mb ?? 10) }}" min="1" max="50" class="w-full px-4 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors">
                    </div>

                    <div>
                        <label for="urutan" class="block text-sm font-medium text-text-primary mb-1">Urutan dalam Kurikulum <span class="text-danger">*</span></label>
                        <input type="number" name="urutan" id="urutan" value="{{ old('urutan', $tugas->urutan) }}" min="1" class="w-full px-4 py-2.5 rounded-lg border border-border bg-[var(--card)] text-text-primary focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" required>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $tugas->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
                    <label for="is_active" class="text-sm font-medium text-text-primary">Aktifkan Tugas ini</label>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-border flex justify-end gap-3">
                <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="px-5 py-2.5 rounded-lg font-medium text-text-secondary hover:bg-secondary border border-transparent transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-lg font-medium text-white bg-primary hover:bg-primary-hover shadow-sm transition-colors flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Perbarui Penugasan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
