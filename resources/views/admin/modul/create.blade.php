@extends('layouts.app')

@section('title', 'Tambah Modul Pembelajaran')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.modul.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-secondary)] hover:text-primary transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Daftar Modul</span>
    </a>
</div>

<div class="max-w-3xl bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl p-6 md:p-8">
    <div class="mb-6 pb-4 border-b border-[var(--border)]">
        <h2 class="text-xl font-bold tracking-tight text-[var(--text-primary)]">Tambah Modul Baru</h2>
        <p class="text-sm text-[var(--text-secondary)] mt-1">Masukkan informasi dasar modul. Anda dapat menambahkan bab materi dan evaluasi setelah modul disimpan.</p>
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

    <form action="{{ route('admin.modul.store') }}" method="POST" class="space-y-5">
        @csrf
        
        <div>
            <label for="judul" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Judul Modul <span class="text-danger">*</span></label>
            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                placeholder="Contoh: Modul 1: Landasan Yuridis Disiplin PNS">
        </div>

        <div>
            <label for="urutan" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Nomor Urutan Modul <span class="text-danger">*</span></label>
            <input type="number" id="urutan" name="urutan" value="{{ old('urutan', 1) }}" required min="1"
                class="w-full sm:w-40 px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
        </div>

        <div>
            <label for="deskripsi" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Deskripsi Ringkas</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"
                class="w-full px-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                placeholder="Ringkasan kompetensi dan capaian pembelajaran yang diharapkan...">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4 text-primary rounded border-[var(--border)] focus:ring-primary">
            <label for="is_active" class="text-sm font-medium text-[var(--text-primary)] cursor-pointer select-none">Aktifkan Modul Ini untuk Peserta</label>
        </div>

        <div class="flex items-center justify-end pt-5 border-t border-[var(--border)] gap-3">
            <a href="{{ route('admin.modul.index') }}" class="btn btn-secondary text-[var(--text-primary)] font-medium py-2.5 px-5 rounded-xl transition-all">Batal</a>
            <button type="submit" class="btn btn-primary text-white font-medium py-2.5 px-6 rounded-xl shadow-xs transition-all flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan Modul</span>
            </button>
        </div>
    </form>
</div>

@endsection
