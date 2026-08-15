@extends('layouts.app')

@section('title', 'Edit Video')

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.video.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8">
    <div class="mb-6 pb-4 border-b border-border">
        <h2 class="text-xl font-display font-bold text-text-primary">Edit Video: {{ $video->judul }}</h2>
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

    <form action="{{ route('admin.video.update', $video->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label for="judul" class="block text-sm font-medium text-text-primary mb-1">Judul Video <span class="text-danger">*</span></label>
            <input type="text" id="judul" name="judul" value="{{ old('judul', $video->judul) }}" required
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
        </div>

        <div>
            <label for="url_youtube" class="block text-sm font-medium text-text-primary mb-1">Tautan YouTube <span class="text-danger">*</span></label>
            <input type="url" id="url_youtube" name="url_youtube" value="{{ old('url_youtube', $video->url_youtube) }}" required
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-text-primary mb-1">Deskripsi Singkat</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('deskripsi', $video->deskripsi) }}</textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" {{ $video->is_active ? 'checked' : '' }} class="w-4 h-4 text-primary rounded border-border focus:ring-primary">
            <label for="is_active" class="text-sm font-medium text-text-primary cursor-pointer">Tampilkan Video Ini</label>
        </div>

        <div class="flex justify-end pt-4 border-t border-border gap-3">
            <a href="{{ route('admin.video.index') }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                Perbarui Video
            </button>
        </div>
    </form>
</div>

@endsection
