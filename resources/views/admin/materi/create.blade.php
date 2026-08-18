@extends('layouts.app')

@section('title', 'Tambah Materi')

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Modul
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8 max-w-3xl">
    <div class="mb-6 pb-4 border-b border-border">
        <h2 class="text-xl font-display font-bold text-text-primary">Tambah Materi ke: {{ $pelatihan->judul }}</h2>
        <p class="text-text-secondary mt-1">Pilih jenis materi (Dokumen, Video, Link, atau Kuis) dan lengkapi informasinya.</p>
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

    <form action="{{ route('admin.pelatihan.materi.store', $pelatihan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="judul" class="block text-sm font-medium text-text-primary mb-1">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            </div>

            <div>
                <label for="jenis" class="block text-sm font-medium text-text-primary mb-1">Jenis Konten <span class="text-danger">*</span></label>
                <select id="jenis" name="jenis" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white" onchange="toggleContentInput(this.value)">
                    <option value="pdf" {{ old('jenis') == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                    <option value="ppt" {{ old('jenis') == 'ppt' ? 'selected' : '' }}>PowerPoint (PPT)</option>
                    <option value="pptx" {{ old('jenis') == 'pptx' ? 'selected' : '' }}>PowerPoint (PPTX)</option>
                    <option value="video_embed" {{ old('jenis') == 'video_embed' ? 'selected' : '' }}>Embed Video (YouTube)</option>
                    <option value="link" {{ old('jenis') == 'link' ? 'selected' : '' }}>Tautan Eksternal</option>
                </select>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="urutan" class="block text-sm font-medium text-text-primary mb-1">Urutan <span class="text-danger">*</span></label>
                    <input type="number" id="urutan" name="urutan" value="{{ old('urutan', 1) }}" required min="1"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
                <div class="flex-1">
                    <label for="durasi_baca" class="block text-sm font-medium text-text-primary mb-1">Estimasi Waktu Baca (Mnt) <span class="text-danger">*</span></label>
                    <input type="number" id="durasi_baca" name="durasi_baca" value="{{ old('durasi_baca', 15) }}" required min="1"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
                <div class="flex-1">
                    <label for="poin" class="block text-sm font-medium text-text-primary mb-1">Poin Penyelesaian <span class="text-danger">*</span></label>
                    <input type="number" id="poin" name="poin" value="{{ old('poin', 50) }}" required min="0"
                        class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                </div>
            </div>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-text-primary mb-1">Deskripsi / Instruksi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('deskripsi') }}</textarea>
        </div>

        <div id="file_input_container" class="{{ in_array(old('jenis'), ['link', 'video_embed']) ? 'hidden' : '' }}">
            <label for="file_upload" class="block text-sm font-medium text-text-primary mb-1">Unggah Dokumen (PDF/PPT/PPTX)</label>
            <input type="file" id="file_upload" name="file_upload" accept=".pdf,.ppt,.pptx"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
            <p class="text-xs text-text-secondary mt-1">Maksimal ukuran file: 10MB.</p>
        </div>

        <div id="url_input_container" class="{{ in_array(old('jenis'), ['link', 'video_embed']) ? '' : 'hidden' }}">
            <label for="url_link" class="block text-sm font-medium text-text-primary mb-1">Tautan / URL</label>
            <input type="url" id="url_link" name="url_link" value="{{ old('url_link') }}" placeholder="https://..."
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-border mt-4">
            <input type="checkbox" id="is_active" name="is_active" checked class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
            <label for="is_active" class="text-sm font-medium text-text-primary cursor-pointer">Materi Aktif</label>
        </div>

        <div class="flex justify-end pt-4 gap-3">
            <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                Simpan Materi
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
    
    // Hide all
    fileContainer.classList.add('hidden');
    urlContainer.classList.add('hidden');

    if (jenis === 'link' || jenis === 'video_embed') {
        urlContainer.classList.remove('hidden');
    } else {
        fileContainer.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    toggleContentInput(document.getElementById('jenis').value);
});
</script>
@endpush
