@extends('layouts.app')

@section('title', 'Manajemen Video Orientasi')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-display font-bold text-primary">Video Orientasi</h1>
        <p class="text-text-secondary mt-1">Kelola video pengantar yang dapat ditonton oleh semua pengguna.</p>
    </div>
    <a href="{{ route('admin.video.create') }}" class="bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-bold py-2.5 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-5 h-5"></i> Tambah Video
    </a>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-text-secondary uppercase bg-secondary border-b border-border">
                <tr>
                    <th class="px-6 py-3 w-16 text-center">No</th>
                    <th class="px-6 py-3">Judul Video</th>
                    <th class="px-6 py-3">Sumber / URL</th>
                    <th class="px-6 py-3 w-32 text-center">Status</th>
                    <th class="px-6 py-3 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($videos as $video)
                    <tr class="border-b border-border hover:bg-secondary/30 transition-colors">
                        <td class="px-6 py-4 text-center text-text-secondary">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-text-primary">{{ $video->judul }}</p>
                            <p class="text-xs text-text-secondary line-clamp-1 mt-0.5">{{ $video->deskripsi }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ $video->url_youtube }}" target="_blank" class="text-primary hover:underline text-xs flex items-center gap-1">
                                <i data-lucide="external-link" class="w-3 h-3"></i> Tonton
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($video->is_active)
                                <span class="bg-success/10 text-success text-xs font-bold px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="bg-danger/10 text-danger text-xs font-bold px-2 py-1 rounded">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.video.edit', $video->id) }}" class="inline-block border border-border text-text-secondary hover:text-primary hover:border-primary px-2 py-1.5 rounded transition-colors" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.video.destroy', $video->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus video ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="border border-border text-text-secondary hover:text-danger hover:border-danger px-2 py-1.5 rounded transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-text-secondary">Belum ada video orientasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

