@extends('layouts.app')

@section('title', 'Tambah Modul Pembelajaran')

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.materi.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8">
    <div class="mb-6 pb-4 border-b border-border">
        <h2 class="text-xl font-display font-bold text-text-primary">Tambah Modul Baru</h2>
        <p class="text-text-secondary mt-1">Masukkan informasi modul dan unggah file referensi.</p>
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

    <form action="{{ route('admin.materi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="judul" class="block text-sm font-medium text-text-primary mb-1">Judul Modul <span class="text-danger">*</span></label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            </div>

            <div>
                <label for="jabatan_id" class="block text-sm font-medium text-text-primary mb-1">Diperuntukkan Bagi Jabatan <span class="text-danger">*</span></label>
                <select id="jabatan_id" name="jabatan_id" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                            {{ $jabatan->nama_jabatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="urutan" class="block text-sm font-medium text-text-primary mb-1">Urutan Pembelajaran <span class="text-danger">*</span></label>
                <input type="number" id="urutan" name="urutan" value="{{ old('urutan', 1) }}" required min="1"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            </div>

            <div>
                <label for="durasi_baca" class="block text-sm font-medium text-text-primary mb-1">Estimasi Durasi (Menit) <span class="text-danger">*</span></label>
                <input type="number" id="durasi_baca" name="durasi_baca" value="{{ old('durasi_baca', 15) }}" required min="1"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            </div>
            
            <div>
                <label for="jenis" class="block text-sm font-medium text-text-primary mb-1">Jenis Konten <span class="text-danger">*</span></label>
                <select id="jenis" name="jenis" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white" onchange="toggleContentInput(this.value)">
                    <option value="pdf" {{ old('jenis') == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                    <option value="ppt" {{ old('jenis') == 'ppt' ? 'selected' : '' }}>PowerPoint (PPT)</option>
                    <option value="pptx" {{ old('jenis') == 'pptx' ? 'selected' : '' }}>PowerPoint (PPTX)</option>
                    <option value="link" {{ old('jenis') == 'link' ? 'selected' : '' }}>Tautan Eksternal</option>
                    <option value="video_embed" {{ old('jenis') == 'video_embed' ? 'selected' : '' }}>Embed Video (YouTube)</option>
                </select>
            </div>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-text-primary mb-1">Deskripsi Singkat</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('deskripsi') }}</textarea>
        </div>

        <div id="file_input_container" class="{{ in_array(old('jenis'), ['link', 'video_embed']) ? 'hidden' : '' }}">
            <label for="file_upload" class="block text-sm font-medium text-text-primary mb-1">Unggah Dokumen (PDF/PPT/PPTX)</label>
            <input type="file" id="file_upload" name="file_upload" accept=".pdf,.ppt,.pptx"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            <p class="text-xs text-text-secondary mt-1">Maksimal ukuran file: 20MB.</p>
        </div>

        <div id="url_input_container" class="{{ in_array(old('jenis'), ['link', 'video_embed']) ? '' : 'hidden' }}">
            <label for="url_link" class="block text-sm font-medium text-text-primary mb-1">Tautan / URL Video</label>
            <input type="url" id="url_link" name="url_link" value="{{ old('url_link') }}" placeholder="https://..."
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" checked class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
            <label for="is_active" class="text-sm font-medium text-text-primary cursor-pointer">Aktifkan Modul Ini</label>
        </div>

        <div class="flex justify-end pt-4 border-t border-border gap-3">
            <a href="{{ route('admin.materi.index') }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                Simpan Modul
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
    
    if (jenis === 'link' || jenis === 'video_embed') {
        fileContainer.classList.add('hidden');
        urlContainer.classList.remove('hidden');
    } else {
        fileContainer.classList.remove('hidden');
        urlContainer.classList.add('hidden');
    }
}
// Init on load
document.addEventListener('DOMContentLoaded', () => {
    toggleContentInput(document.getElementById('jenis').value);
});
</script>
@endpush
