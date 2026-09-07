@extends('layouts.app')

@section('title', 'Tambah Video Orientasi')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.video.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-secondary)] hover:text-primary transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar Video</span>
    </a>
</div>

<div class="max-w-3xl bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 md:p-8">
    <div class="mb-6 pb-4 border-b border-[var(--border)]">
        <h2 class="text-xl font-bold tracking-tight text-[var(--text-primary)]">Tambah Video Orientasi</h2>
        <p class="text-sm text-[var(--text-secondary)] mt-1">Masukkan URL video (misal: YouTube) untuk ditampilkan kepada peserta di dashboard.</p>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-danger/10 border border-danger/20 text-danger text-sm mb-6 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.video.store') }}" method="POST" class="space-y-5">
        @csrf
        
        <div>
            <label for="judul" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Judul Video <span class="text-danger">*</span></label>
            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                placeholder="Contoh: Pengantar Disiplin ASN Berdasarkan PP 94 Tahun 2021">
        </div>

        <div>
            <label for="url_youtube" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Tautan YouTube <span class="text-danger">*</span></label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                    <i data-lucide="youtube" class="w-4 h-4 text-red-500"></i>
                </span>
                <input type="url" id="url_youtube" name="url_youtube" value="{{ old('url_youtube') }}" required placeholder="https://www.youtube.com/watch?v=..."
                    class="w-full pl-10 pr-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
            </div>
            <p class="text-xs text-[var(--text-secondary)] mt-1.5">Gunakan link YouTube penuh (contoh: https://www.youtube.com/watch?v=XXXXXX)</p>
        </div>

        <div>
            <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Deskripsi Singkat</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"
                class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                placeholder="Penjelasan ringkas mengenai isi materi video...">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4 text-primary rounded border-[var(--border)] focus:ring-primary">
            <label for="is_active" class="text-sm font-medium text-[var(--text-primary)] cursor-pointer select-none">Publikasikan Video Segera</label>
        </div>

        <div class="flex items-center justify-end pt-5 border-t border-[var(--border)] gap-3">
            <a href="{{ route('admin.video.index') }}" class="btn btn-secondary text-[var(--text-primary)] font-medium py-2.5 px-5 rounded-xl transition-all">Batal</a>
            <button type="submit" class="btn btn-primary text-white font-medium py-2.5 px-6 rounded-xl shadow-xs transition-all flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan Video</span>
            </button>
        </div>
    </form>
</div>

@endsection
