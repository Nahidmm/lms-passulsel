@extends('layouts.app')

@section('title', 'Tambah Agenda Kalender')

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.kalender.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 md:p-8">
    <div class="mb-6 pb-4 border-b border-border">
        <h2 class="text-xl font-display font-bold text-text-primary">Tambah Agenda Baru</h2>
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

    <form action="{{ route('admin.kalender.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-text-primary mb-1">Judul Agenda <span class="text-danger">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-text-primary mb-1">Jenis Agenda <span class="text-danger">*</span></label>
                <select id="type" name="type" required class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white">
                    <option value="libur" {{ old('type') == 'libur' ? 'selected' : '' }}>Hari Libur Nasional</option>
                    <option value="evaluasi" {{ old('type') == 'evaluasi' ? 'selected' : '' }}>Jadwal Evaluasi</option>
                    <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Informasi Umum</option>
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-text-primary mb-1">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-text-primary mb-1">Tanggal Selesai (Opsional)</label>
                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                    class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-sm">
                <p class="text-xs text-text-secondary mt-1">Kosongkan jika agenda hanya berlangsung 1 hari.</p>
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-text-primary mb-1">Keterangan / Deskripsi</label>
            <textarea id="description" name="description" rows="3"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none">{{ old('description') }}</textarea>
        </div>

        <div class="flex justify-end pt-6 border-t border-border gap-3 mt-8">
            <a href="{{ route('admin.kalender.index') }}" class="px-6 py-2 border border-border rounded-lg text-text-secondary hover:bg-secondary font-medium transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm">
                Simpan Agenda
            </button>
        </div>
    </form>
</div>

@endsection
