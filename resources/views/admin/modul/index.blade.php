@extends('layouts.app')

@section('title', 'Manajemen Modul Pembelajaran')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Modul Pembelajaran</h1>
        <p class="text-sm text-[var(--text-secondary)] mt-1">Kelola daftar modul kurikulum disiplin ASN dan urutan pembelajarannya.</p>
    </div>
    <a href="{{ route('admin.modul.create') }}" class="btn btn-primary text-white font-medium py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-xs transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Modul Baru
    </a>
</div>

<div class="bg-[var(--card)] border border-[var(--border)] shadow-xs rounded-2xl overflow-hidden">
    @if($moduls->isEmpty())
        <div class="py-16 text-center text-[var(--text-secondary)]">
            <i data-lucide="book-open" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]"></i>
            <p class="font-medium">Belum ada modul pembelajaran.</p>
            <p class="text-xs text-[var(--text-muted)] mt-1">Klik tombol di atas untuk membuat modul pertama.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-[var(--text-secondary)] uppercase bg-[var(--muted)]/50 border-b border-[var(--border)]">
                    <tr>
                        <th class="px-6 py-3.5 w-20 text-center">Urutan</th>
                        <th class="px-6 py-3.5">Judul Modul</th>
                        <th class="px-6 py-3.5 text-center">Materi Terdaftar</th>
                        <th class="px-6 py-3.5 w-32 text-center">Status</th>
                        <th class="px-6 py-3.5 w-44 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @foreach($moduls as $modul)
                        <tr class="hover:bg-[var(--muted)]/30 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="w-8 h-8 rounded-xl bg-[var(--muted)] flex items-center justify-center font-bold text-xs text-[var(--text-primary)] mx-auto border border-[var(--border)]">
                                    {{ $modul->urutan }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.modul.show', $modul->id) }}" class="font-semibold text-[var(--text-primary)] hover:text-primary transition-colors text-base">
                                    {{ $modul->judul }}
                                </a>
                                <p class="text-xs text-[var(--text-secondary)] line-clamp-1 mt-0.5">{{ $modul->deskripsi }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[var(--muted)] text-[var(--text-primary)] font-medium text-xs border border-[var(--border)]">
                                    <i data-lucide="layers" class="w-3.5 h-3.5 text-primary"></i>
                                    {{ $modul->materis->count() }} Materi
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($modul->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border border-zinc-500/20">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5">
                                <a href="{{ route('admin.modul.show', $modul->id) }}" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-primary hover:border-primary rounded-xl transition-all hover:bg-[var(--card)]" title="Kelola Materi">
                                    <i data-lucide="list" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.modul.edit', $modul->id) }}" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-primary hover:border-primary rounded-xl transition-all hover:bg-[var(--card)]" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.modul.destroy', $modul->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus modul ini beserta seluruh isinya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-danger hover:border-danger rounded-xl transition-all hover:bg-danger/10" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
