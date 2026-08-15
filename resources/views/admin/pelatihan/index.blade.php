@extends('layouts.app')

@section('title', 'Kelola Pelatihan (Course)')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text-primary">Kelola Pelatihan</h1>
        <p class="text-text-secondary mt-1 text-sm">Kelola daftar pelatihan, modul, dan materi secara hierarkis.</p>
    </div>
    <a href="{{ route('admin.pelatihan.create') }}" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pelatihan
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-secondary/50 border-b border-border">
                    <th class="py-4 px-6 font-semibold text-sm text-text-secondary w-16">ID</th>
                    <th class="py-4 px-6 font-semibold text-sm text-text-secondary">Judul Pelatihan</th>
                    <th class="py-4 px-6 font-semibold text-sm text-text-secondary text-center">Status</th>
                    <th class="py-4 px-6 font-semibold text-sm text-text-secondary text-center">Isi</th>
                    <th class="py-4 px-6 font-semibold text-sm text-text-secondary text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($pelatihans as $pelatihan)
                <tr class="hover:bg-secondary/30 transition-colors">
                    <td class="py-4 px-6 text-sm text-text-secondary">#{{ $pelatihan->id }}</td>
                    <td class="py-4 px-6">
                        <div class="font-medium text-text-primary">{{ $pelatihan->judul }}</div>
                        <div class="text-xs text-text-secondary mt-1 truncate max-w-xs">{{ $pelatihan->deskripsi }}</div>
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($pelatihan->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-success/10 text-success border border-success/20">Aktif</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-text-secondary/10 text-text-secondary border border-text-secondary/20">Draf</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="text-xs font-medium text-text-secondary bg-secondary px-2.5 py-1 rounded-full border border-border">
                            {{ $pelatihan->materis->count() }} Materi
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.pelatihan.show', $pelatihan->id) }}" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors tooltip" data-tip="Structure Builder">
                                <i data-lucide="layout-template" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.pelatihan.edit', $pelatihan->id) }}" class="p-2 text-text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.pelatihan.destroy', $pelatihan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pelatihan ini? Semua modul dan materi di dalamnya juga akan terhapus!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-text-secondary hover:text-danger hover:bg-danger/10 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 px-6 text-center text-text-secondary">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="book-open" class="w-12 h-12 text-border mb-3"></i>
                            <p>Belum ada data pelatihan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pelatihans->hasPages())
    <div class="p-4 border-t border-border">
        {{ $pelatihans->links() }}
    </div>
    @endif
</div>
@endsection
