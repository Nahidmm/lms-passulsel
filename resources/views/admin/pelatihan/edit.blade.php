@extends('layouts.app')
@section('title', 'Edit Pelatihan: ' . $pelatihan->judul)

@section('content')
<div class="space-y-6 py-2 max-w-3xl mx-auto">
    {{-- Back link --}}
    <div>
        <a href="{{ route('admin.pelatihan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--text-secondary)] hover:text-indigo-600 transition-colors">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Pelatihan
        </a>
    </div>

    <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden shadow-xs">
        <div class="p-5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <div>
                <h1 class="text-base font-bold text-[var(--text-primary)]">Edit Kursus Pelatihan</h1>
                <p class="text-xs text-[var(--text-secondary)]">Perbarui informasi dan pengaturan status publikasi kursus</p>
            </div>
            <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="text-xs font-semibold text-indigo-600 hover:underline flex items-center gap-1">
                <i data-lucide="sliders" class="w-3.5 h-3.5"></i> Buka Kurikulum
            </a>
        </div>
        
        <div class="p-6 md:p-8">
            <form action="{{ route('admin.pelatihan.update', $pelatihan->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="judul" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Judul Pelatihan <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul', $pelatihan->judul) }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors" required>
                    @error('judul') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Deskripsi & Capaian Pembelajaran</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors leading-relaxed">{{ old('deskripsi', $pelatihan->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="p-3.5 rounded-xl border border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50 flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $pelatihan->is_active) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-[var(--border)] focus:ring-indigo-500">
                    <div>
                        <label for="is_active" class="text-xs font-bold text-[var(--text-primary)] block cursor-pointer">Publikasikan Pelatihan (Status Aktif)</label>
                        <p class="text-[11px] text-[var(--text-secondary)]">Peserta dapat mengakses materi pelatihan ini jika status aktif diaktifkan.</p>
                    </div>
                </div>

                <div class="pt-5 border-t border-[var(--border)] flex justify-end gap-3">
                    <a href="{{ route('admin.pelatihan.index') }}" class="btn btn-secondary text-xs px-4 py-2">Batal</a>
                    <button type="submit" class="btn btn-primary text-xs px-5 py-2">Perbarui Pelatihan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
