@extends('layouts.app')

@section('title', 'Edit Materi')

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.modul.show', $materi->modul_id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Modul
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8 max-w-3xl">
    <div class="mb-6 pb-4 border-b border-border">
        <h2 class="text-xl font-display font-bold text-text-primary">Edit Materi: {{ $materi->judul }}</h2>
    </div>

    @if($errors->any())
        <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.materi.update', $materi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="judul" class="block text-sm font-medium text-text-primary mb-1">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" required
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            </div>

            <div>
                <label for="jenis" class="block text-sm font-medium text-text-primary mb-1">Jenis Konten <span class="text-danger">*</span></label>
                <select id="jenis" name="jenis" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white" onchange="toggleContentInput(this.value)">
                    <option value="pdf" {{ old('jenis', $materi->jenis) == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                    <option value="ppt" {{ old('jenis', $materi->jenis) == 'ppt' ? 'selected' : '' }}>PowerPoint (PPT)</option>
                    <option value="pptx" {{ old('jenis', $materi->jenis) == 'pptx' ? 'selected' : '' }}>PowerPoint (PPTX)</option>
                    <option value="video_embed" {{ old('jenis', $materi->jenis) == 'video_embed' ? 'selected' : '' }}>Embed Video (YouTube)</option>
                    <option value="link" {{ old('jenis', $materi->jenis) == 'link' ? 'selected' : '' }}>Tautan Eksternal</option>
                    <option value="quiz" {{ old('jenis', $materi->jenis) == 'quiz' ? 'selected' : '' }}>Kuis / Evaluasi</option>
                </select>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="urutan" class="block text-sm font-medium text-text-primary mb-1">Urutan <span class="text-danger">*</span></label>
                    <input type="number" id="urutan" name="urutan" value="{{ old('urutan', $materi->urutan) }}" required min="1"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
                <div class="flex-1">
                    <label for="durasi_baca" class="block text-sm font-medium text-text-primary mb-1">Durasi (Mnt) <span class="text-danger">*</span></label>
                    <input type="number" id="durasi_baca" name="durasi_baca" value="{{ old('durasi_baca', $materi->durasi_baca) }}" required min="1"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
            </div>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-text-primary mb-1">Deskripsi / Instruksi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
        </div>

        <div id="file_input_container" class="{{ in_array(old('jenis', $materi->jenis), ['link', 'video_embed', 'quiz']) ? 'hidden' : '' }}">
            <label for="file_upload" class="block text-sm font-medium text-text-primary mb-1">Unggah Dokumen (Ganti File)</label>
            <input type="file" id="file_upload" name="file_upload" accept=".pdf,.ppt,.pptx"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
            <p class="text-xs text-text-secondary mt-1">Biarkan kosong jika tidak ingin mengubah. Maks 10MB.</p>
            @if($materi->file_path && !in_array($materi->jenis, ['link', 'video_embed', 'quiz']))
                <div class="mt-2 text-sm text-primary font-medium flex items-center gap-1">
                    <i data-lucide="file-check-2" class="w-4 h-4"></i> File saat ini tersedia.
                </div>
            @endif
        </div>

        <div id="url_input_container" class="{{ in_array(old('jenis', $materi->jenis), ['link', 'video_embed']) ? '' : 'hidden' }}">
            <label for="url_link" class="block text-sm font-medium text-text-primary mb-1">Tautan / URL</label>
            <input type="url" id="url_link" name="url_link" value="{{ old('url_link', $materi->url_link) }}" placeholder="https://..."
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
        </div>

        <div id="quiz_info_container" class="{{ old('jenis', $materi->jenis) == 'quiz' ? '' : 'hidden' }}">
            <div class="bg-accent/10 border border-accent/20 rounded-lg p-4 flex items-start gap-3 text-accent-hover text-sm">
                <i data-lucide="info" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <p>Untuk mengedit soal-soal di dalam kuis ini, gunakan menu kelola soal dari halaman Modul.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-border mt-4">
            <input type="checkbox" id="is_active" name="is_active" {{ $materi->is_active ? 'checked' : '' }} class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
            <label for="is_active" class="text-sm font-medium text-text-primary cursor-pointer">Materi Aktif</label>
        </div>

        <div class="flex justify-end pt-4 gap-3">
            <a href="{{ route('admin.modul.show', $materi->modul_id) }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                Perbarui Materi
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function toggleContentInput(jenis) {
    const fileContainer = document.getElementById('file_input_container');
    const urlContainer = document.getElementById('url_input_container');
    const quizContainer = document.getElementById('quiz_info_container');
    
    // Hide all
    fileContainer.classList.add('hidden');
    urlContainer.classList.add('hidden');
    quizContainer.classList.add('hidden');

    if (jenis === 'link' || jenis === 'video_embed') {
        urlContainer.classList.remove('hidden');
    } else if (jenis === 'quiz') {
        quizContainer.classList.remove('hidden');
    } else {
        fileContainer.classList.remove('hidden');
    }
}
</script>
@endpush
