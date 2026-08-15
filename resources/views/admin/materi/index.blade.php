@extends('layouts.app')

@section('title', 'Manajemen Modul Pembelajaran')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-display font-bold text-primary">Modul Pembelajaran</h1>
        <p class="text-text-secondary mt-1">Kelola urutan dan daftar modul untuk setiap jabatan Eselon V.</p>
    </div>
    <a href="#" class="bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-5 h-5"></i> Tambah Modul Baru
    </a>
</div>

<div class="space-y-8">
    @foreach($jabatans as $jabatan)
        <div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
            <div class="p-4 bg-secondary/50 border-b border-border flex justify-between items-center">
                <h3 class="font-display font-bold text-text-primary text-lg flex items-center gap-2">
                    <i data-lucide="briefcase" class="w-5 h-5 text-accent"></i> {{ $jabatan->nama_jabatan }}
                </h3>
                <span class="text-xs bg-white border border-border px-2 py-1 rounded font-medium text-text-secondary">{{ $jabatan->materis->count() }} Modul</span>
            </div>
            
            <div class="p-0">
                @if($jabatan->materis->isEmpty())
                    <p class="text-center text-text-secondary py-6 text-sm">Belum ada modul untuk jabatan ini.</p>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-text-secondary uppercase bg-white border-b border-border">
                            <tr>
                                <th class="px-6 py-3 w-20 text-center">Urutan</th>
                                <th class="px-6 py-3">Judul Modul</th>
                                <th class="px-6 py-3 w-32">Jenis</th>
                                <th class="px-6 py-3 w-32 text-center">Status</th>
                                <th class="px-6 py-3 w-32 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jabatan->materis as $materi)
                                <tr class="border-b border-border last:border-0 hover:bg-secondary/30 transition-colors">
                                    <td class="px-6 py-4 text-center">
                                        <span class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center font-bold text-text-primary mx-auto border border-border">
                                            {{ $materi->urutan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-text-primary">{{ $materi->judul }}</p>
                                        <p class="text-xs text-text-secondary line-clamp-1 mt-0.5">{{ $materi->deskripsi }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-medium text-text-secondary flex items-center gap-1 uppercase tracking-wider">
                                            @if($materi->jenis === 'video_embed') <i data-lucide="video" class="w-3 h-3"></i> Video
                                            @elseif($materi->jenis === 'link') <i data-lucide="link" class="w-3 h-3"></i> Tautan
                                            @else <i data-lucide="file-text" class="w-3 h-3"></i> {{ $materi->jenis }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($materi->is_active)
                                            <span class="bg-success/10 text-success text-xs font-bold px-2 py-1 rounded">Aktif</span>
                                        @else
                                            <span class="bg-danger/10 text-danger text-xs font-bold px-2 py-1 rounded">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="#" class="inline-block border border-border text-text-secondary hover:text-primary hover:border-primary px-2 py-1.5 rounded transition-colors" title="Edit">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.materi.destroy', $materi->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus modul ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border border-border text-text-secondary hover:text-danger hover:border-danger px-2 py-1.5 rounded transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endforeach
</div>

@endsection
