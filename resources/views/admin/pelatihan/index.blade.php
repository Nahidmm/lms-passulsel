@extends('layouts.app')
@section('title', 'Kelola Pelatihan')

@section('content')
<div class="space-y-5 py-1">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="sep">/</span>
        <span class="current">Kelola Pelatihan</span>
    </div>

    {{-- Page heading --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[var(--text-primary)]">Kelola Pelatihan</h1>
            <p class="text-[var(--text-secondary)] text-sm mt-1">Kelola daftar pelatihan, modul, dan materi secara hierarkis.</p>
        </div>
        <a href="{{ route('admin.pelatihan.create') }}" class="btn btn-primary shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pelatihan
        </a>
    </div>

    {{-- Table card --}}
    <div class="game-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/7 bg-[var(--card)] border border-[var(--border)] shadow-sm">
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)] w-16">ID</th>
                        <th class="py-3.5 px-5 text-left text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Judul Pelatihan</th>
                        <th class="py-3.5 px-5 text-center text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Status</th>
                        <th class="py-3.5 px-5 text-center text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Materi</th>
                        <th class="py-3.5 px-5 text-right text-xs font-black uppercase tracking-widest text-[var(--text-muted)]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse($pelatihans as $pelatihan)
                        <tr class="hover:bg-[var(--card)] border border-[var(--border)] shadow-sm transition-colors">
                            <td class="py-4 px-5 text-sm text-[var(--text-muted)] font-mono">#{{ $pelatihan->id }}</td>
                            <td class="py-4 px-5">
                                <div class="font-semibold text-[var(--text-primary)]">{{ $pelatihan->judul }}</div>
                                <div class="text-xs text-[var(--text-muted)] mt-0.5 max-w-xs"
                                     title="{{ $pelatihan->deskripsi }}">
                                    {{ Str::limit($pelatihan->deskripsi, 80) }}
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($pelatihan->is_active)
                                    <span class="badge badge-emerald text-[10px]"><i data-lucide="check-circle" class="w-2.5 h-2.5"></i> Aktif</span>
                                @else
                                    <span class="badge badge-rose text-[10px]"><i data-lucide="circle" class="w-2.5 h-2.5"></i> Draf</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="badge badge-violet text-[10px]">{{ $pelatihan->materis->count() }} Materi</span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Structure builder --}}
                                    <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}"
                                       class="action-btn is-view" title="Structure Builder">
                                        <i data-lucide="layout-template" class="w-4 h-4"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.pelatihan.edit', $pelatihan->id) }}"
                                       class="action-btn is-edit" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    {{-- Delete - always red --}}
                                    <form action="{{ route('admin.pelatihan.destroy', $pelatihan->id) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('Yakin hapus pelatihan ini? Semua modul dan materi akan ikut terhapus!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn is-delete" title="Hapus Pelatihan">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i data-lucide="book-open" class="w-6 h-6"></i>
                                    </div>
                                    <h4>Belum Ada Pelatihan</h4>
                                    <p>Tambahkan pelatihan pertama untuk mulai mengelola konten pembelajaran.</p>
                                    <a href="{{ route('admin.pelatihan.create') }}" class="btn btn-primary mt-4 text-sm">
                                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pelatihan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pelatihans->hasPages())
            <div class="p-4 border-t border-[var(--border)]">
                {{ $pelatihans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

