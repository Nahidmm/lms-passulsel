@extends('layouts.app')

@section('title', 'Manajemen Video Orientasi')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Video Orientasi</h1>
        <p class="text-sm text-[var(--text-secondary)] mt-1">Kelola video pembekalan dan pengantar disiplin ASN.</p>
    </div>
    <a href="{{ route('admin.video.create') }}" class="btn btn-primary text-white font-medium py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-xs transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Video
    </a>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-[var(--text-secondary)] uppercase bg-[var(--muted)]/50 border-b border-[var(--border)]">
                <tr>
                    <th class="px-6 py-3.5 w-16 text-center">No</th>
                    <th class="px-6 py-3.5">Judul Video</th>
                    <th class="px-6 py-3.5">Sumber / URL</th>
                    <th class="px-6 py-3.5 w-32 text-center">Status</th>
                    <th class="px-6 py-3.5 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border)]">
                @forelse($videos as $video)
                    <tr class="hover:bg-[var(--muted)]/30 transition-colors">
                        <td class="px-6 py-4 text-center text-[var(--text-secondary)] font-mono text-xs">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-[var(--text-primary)]">{{ $video->judul }}</p>
                            <p class="text-xs text-[var(--text-secondary)] line-clamp-1 mt-0.5">{{ $video->deskripsi }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ $video->url_youtube }}" target="_blank" class="text-primary hover:underline text-xs inline-flex items-center gap-1 font-medium">
                                <i data-lucide="youtube" class="w-3.5 h-3.5 text-red-500"></i>
                                <span>Buka Video</span>
                                <i data-lucide="external-link" class="w-3 h-3 opacity-60"></i>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($video->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border border-zinc-500/20">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-1.5">
                            <a href="{{ route('admin.video.edit', $video->id) }}" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-primary hover:border-primary rounded-xl transition-all hover:bg-[var(--card)]" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.video.destroy', $video->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus video ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-danger hover:border-danger rounded-xl transition-all hover:bg-danger/10" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-[var(--text-secondary)]">
                            <i data-lucide="video-off" class="w-8 h-8 mx-auto mb-2 text-[var(--text-muted)]"></i>
                            <p>Belum ada video orientasi.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
