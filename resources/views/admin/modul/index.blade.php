@extends('layouts.app')

@section('title', 'Manajemen Modul Pembelajaran')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-display font-bold text-primary">Modul Pembelajaran</h1>
        <p class="text-text-secondary mt-1">Kelola daftar modul utama yang dapat diakses oleh semua pengguna.</p>
    </div>
    <a href="{{ route('admin.modul.create') }}" class="bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-bold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-5 h-5"></i> Tambah Modul Baru
    </a>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border overflow-hidden">
    @if($moduls->isEmpty())
        <p class="text-center text-text-secondary py-6 text-sm">Belum ada modul. Silakan tambah modul baru.</p>
    @else
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-text-secondary uppercase bg-secondary/50 border-b border-border">
                <tr>
                    <th class="px-6 py-4 w-20 text-center">Urutan</th>
                    <th class="px-6 py-4">Judul Modul</th>
                    <th class="px-6 py-4 text-center">Jumlah Materi</th>
                    <th class="px-6 py-4 w-32 text-center">Status</th>
                    <th class="px-6 py-4 w-40 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($moduls as $modul)
                    <tr class="border-b border-border last:border-0 hover:bg-secondary/30 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <span class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center font-bold text-text-primary mx-auto border border-border">
                                {{ $modul->urutan }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-text-primary text-base">{{ $modul->judul }}</p>
                            <p class="text-xs text-text-secondary line-clamp-1 mt-1">{{ $modul->deskripsi }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-secondary text-text-primary font-medium text-xs border border-border">
                                <i data-lucide="layers" class="w-3.5 h-3.5 text-accent"></i>
                                {{ $modul->materis->count() }} Materi
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($modul->is_active)
                                <span class="bg-success/10 text-success text-xs font-bold px-2.5 py-1 rounded-full border border-success/20">Aktif</span>
                            @else
                                <span class="bg-danger/10 text-danger text-xs font-bold px-2.5 py-1 rounded-full border border-danger/20">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.modul.show', $modul->id) }}" class="inline-block border border-border text-text-secondary hover:text-primary hover:border-primary px-2.5 py-1.5 rounded transition-colors bg-[var(--card)] border border-[var(--border)] shadow-sm shadow-sm" title="Kelola Materi">
                                <i data-lucide="list" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.modul.edit', $modul->id) }}" class="inline-block border border-border text-text-secondary hover:text-primary hover:border-primary px-2.5 py-1.5 rounded transition-colors bg-[var(--card)] border border-[var(--border)] shadow-sm shadow-sm" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.modul.destroy', $modul->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus modul ini berserta seluruh isinya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="border border-border text-text-secondary hover:text-danger hover:border-danger px-2.5 py-1.5 rounded transition-colors bg-[var(--card)] border border-[var(--border)] shadow-sm shadow-sm" title="Hapus">
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

@endsection

