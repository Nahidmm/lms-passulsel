@extends('layouts.app')
@section('title', 'Kelola Pelatihan')

@section('content')
<div class="space-y-6">

    {{-- Page heading --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Kelola Pelatihan</h1>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Kelola kurikulum pelatihan kedinasan, modul, dan materi secara hierarkis.</p>
        </div>
        <a href="{{ route('admin.pelatihan.create') }}" class="btn btn-primary text-white font-medium py-2.5 px-4 rounded-xl flex items-center gap-2 shadow-xs transition-all shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Pelatihan</span>
        </a>
    </div>

    {{-- Table card --}}
    <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-[var(--text-secondary)] uppercase bg-[var(--muted)]/50 border-b border-[var(--border)]">
                    <tr>
                        <th class="py-3.5 px-6 text-center w-16">ID</th>
                        <th class="py-3.5 px-6">Judul Pelatihan</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Materi Terdaftar</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($pelatihans as $pelatihan)
                        <tr class="hover:bg-[var(--muted)]/30 transition-colors">
                            <td class="py-4 px-6 text-xs text-[var(--text-secondary)] font-mono text-center">#{{ $pelatihan->id }}</td>
                            <td class="py-4 px-6">
                                <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="font-semibold text-base text-[var(--text-primary)] hover:text-primary transition-colors">
                                    {{ $pelatihan->judul }}
                                </a>
                                <p class="text-xs text-[var(--text-secondary)] mt-0.5 max-w-lg line-clamp-1" title="{{ $pelatihan->deskripsi }}">
                                    {{ $pelatihan->deskripsi }}
                                </p>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($pelatihan->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border border-zinc-500/20">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[var(--muted)] text-[var(--text-primary)] font-medium text-xs border border-[var(--border)]">
                                    <i data-lucide="layers" class="w-3.5 h-3.5 text-primary"></i>
                                    {{ $pelatihan->materis->count() }} Materi
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-1.5">
                                {{-- Structure builder --}}
                                <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}"
                                   class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-primary hover:border-primary rounded-xl transition-all hover:bg-[var(--card)]" title="Kelola Kurikulum & Materi">
                                    <i data-lucide="layout-template" class="w-4 h-4"></i>
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('admin.pelatihan.edit', $pelatihan->id) }}"
                                   class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-primary hover:border-primary rounded-xl transition-all hover:bg-[var(--card)]" title="Edit Data Pelatihan">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('admin.pelatihan.destroy', $pelatihan->id) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Yakin hapus pelatihan ini? Semua modul dan materi akan ikut terhapus!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex p-2 border border-[var(--border)] text-[var(--text-secondary)] hover:text-danger hover:border-danger rounded-xl transition-all hover:bg-danger/10" title="Hapus Pelatihan">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-[var(--text-secondary)]">
                                <i data-lucide="book-open" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]"></i>
                                <p class="font-medium">Belum Ada Pelatihan</p>
                                <p class="text-xs text-[var(--text-muted)] mt-1">Tambahkan program pelatihan pertama untuk mulai mengisi materi ajar.</p>
                                <a href="{{ route('admin.pelatihan.create') }}" class="btn btn-primary text-white font-medium py-2 px-4 rounded-xl inline-flex items-center gap-2 mt-4 text-xs">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Pelatihan
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pelatihans->hasPages())
            <div class="p-4 border-t border-[var(--border)] flex justify-center">
                {{ $pelatihans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
