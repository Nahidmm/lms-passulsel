@extends('layouts.app')

@section('title', 'Edit Pelatihan')

@section('content')
<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('admin.pelatihan.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden max-w-3xl">
    <div class="px-6 py-4 border-b border-border bg-secondary/30">
        <h2 class="text-lg font-bold text-text-primary">Edit Pelatihan: {{ $pelatihan->judul }}</h2>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.pelatihan.update', $pelatihan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <div>
                    <label for="judul" class="block text-sm font-medium text-text-primary mb-1">Judul Pelatihan <span class="text-danger">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul', $pelatihan->judul) }}" class="w-full px-4 py-2.5 rounded-lg border border-border focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" required>
                    @error('judul') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-text-primary mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-border focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors">{{ old('deskripsi', $pelatihan->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $pelatihan->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
                    <label for="is_active" class="text-sm text-text-primary">Publish Pelatihan (Aktif)</label>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-border flex justify-end gap-3">
                <a href="{{ route('admin.pelatihan.index') }}" class="px-5 py-2.5 rounded-lg font-medium text-text-secondary hover:bg-secondary border border-transparent transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-lg font-medium text-white bg-primary hover:bg-primary-hover shadow-sm transition-colors">Perbarui Pelatihan</button>
            </div>
        </form>
    </div>
</div>
@endsection
