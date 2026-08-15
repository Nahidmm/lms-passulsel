@extends('layouts.app')

@section('title', 'Detail Modul: ' . $modul->judul)

@section('content')

<div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.modul.index') }}" class="text-text-secondary hover:text-primary flex items-center gap-1 font-medium transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Modul
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden mb-8">
    <div class="p-6 md:p-8 bg-secondary/30">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-display font-bold text-text-primary mb-2">{{ $modul->judul }}</h2>
                <p class="text-text-secondary">{{ $modul->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-center bg-white px-4 py-2 border border-border rounded-lg shadow-sm">
                    <p class="text-xs text-text-secondary font-medium uppercase tracking-wider mb-1">Status</p>
                    @if($modul->is_active)
                        <span class="text-success font-bold text-sm">Aktif</span>
                    @else
                        <span class="text-danger font-bold text-sm">Draft</span>
                    @endif
                </div>
                <a href="{{ route('admin.modul.edit', $modul->id) }}" class="bg-white hover:bg-secondary border border-border text-text-primary px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 shadow-sm">
                    <i data-lucide="edit-2" class="w-4 h-4"></i> Edit Modul
                </a>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h3 class="text-xl font-display font-bold text-text-primary">Materi & Kuis</h3>
        <p class="text-text-secondary mt-1">Kelola konten pembelajaran di dalam modul ini.</p>
    </div>
    <a href="{{ route('admin.modul.materi.create', $modul->id) }}" class="bg-accent hover:bg-accent-hover text-white font-bold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-5 h-5"></i> Tambah Materi / Kuis
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
    @if($materis->isEmpty())
        <p class="text-center text-text-secondary py-8">Belum ada materi di dalam modul ini.</p>
    @else
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-text-secondary uppercase bg-secondary/50 border-b border-border">
                <tr>
                    <th class="px-6 py-4 w-20 text-center">Urutan</th>
                    <th class="px-6 py-4">Judul Materi</th>
                    <th class="px-6 py-4 w-32">Jenis Konten</th>
                    <th class="px-6 py-4 w-32 text-center">Durasi</th>
                    <th class="px-6 py-4 w-32 text-center">Status</th>
                    <th class="px-6 py-4 w-40 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materis as $materi)
                    <tr class="border-b border-border last:border-0 hover:bg-secondary/30 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <span class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center font-bold text-text-primary mx-auto border border-border">
                                {{ $materi->urutan }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-text-primary text-base">{{ $materi->judul }}</p>
                            @if($materi->jenis === 'quiz')
                                <p class="text-xs text-text-secondary mt-1">{{ $materi->soals->count() }} Soal</p>
                            @else
                                <p class="text-xs text-text-secondary line-clamp-1 mt-1">{{ $materi->deskripsi }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-text-secondary flex items-center gap-1.5 uppercase tracking-wider">
                                @if($materi->jenis === 'video_embed') <i data-lucide="video" class="w-4 h-4 text-primary"></i> Video
                                @elseif($materi->jenis === 'link') <i data-lucide="link" class="w-4 h-4 text-primary"></i> Link
                                @elseif($materi->jenis === 'quiz') <i data-lucide="help-circle" class="w-4 h-4 text-accent"></i> Kuis
                                @else <i data-lucide="file-text" class="w-4 h-4 text-primary"></i> {{ $materi->jenis }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-text-secondary">{{ $materi->durasi_baca }} mnt</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($materi->is_active)
                                <span class="bg-success/10 text-success text-xs font-bold px-2.5 py-1 rounded-full border border-success/20">Aktif</span>
                            @else
                                <span class="bg-danger/10 text-danger text-xs font-bold px-2.5 py-1 rounded-full border border-danger/20">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($materi->jenis === 'quiz')
                                <a href="{{ route('admin.materi.soal.create', $materi->id) }}" class="inline-block border border-border text-text-secondary hover:text-accent hover:border-accent px-2.5 py-1.5 rounded transition-colors bg-white shadow-sm" title="Kelola Soal">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </a>
                            @endif
                            <a href="{{ route('admin.materi.edit', $materi->id) }}" class="inline-block border border-border text-text-secondary hover:text-primary hover:border-primary px-2.5 py-1.5 rounded transition-colors bg-white shadow-sm" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.materi.destroy', $materi->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="border border-border text-text-secondary hover:text-danger hover:border-danger px-2.5 py-1.5 rounded transition-colors bg-white shadow-sm" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    @if($materi->jenis === 'quiz' && $materi->soals->isNotEmpty())
                        <tr class="bg-secondary/10 border-b border-border">
                            <td colspan="6" class="p-4 pl-20">
                                <div class="bg-white border border-border rounded-lg shadow-sm">
                                    <table class="w-full text-sm text-left">
                                        <thead class="text-xs text-text-secondary uppercase bg-secondary/30">
                                            <tr>
                                                <th class="px-4 py-2 w-10 text-center">#</th>
                                                <th class="px-4 py-2">Pertanyaan</th>
                                                <th class="px-4 py-2 w-24 text-center">Bobot</th>
                                                <th class="px-4 py-2 w-32 text-right">Aksi Soal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($materi->soals as $idx => $soal)
                                            <tr class="border-t border-border">
                                                <td class="px-4 py-2 text-center text-text-secondary">{{ $idx + 1 }}</td>
                                                <td class="px-4 py-2 line-clamp-1 text-text-primary">{{ $soal->pertanyaan }}</td>
                                                <td class="px-4 py-2 text-center text-text-secondary">{{ $soal->bobot }}</td>
                                                <td class="px-4 py-2 text-right space-x-1">
                                                    <a href="{{ route('admin.soal.edit', $soal->id) }}" class="text-primary hover:underline text-xs">Edit</a>
                                                    <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus soal ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-danger hover:underline text-xs">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
