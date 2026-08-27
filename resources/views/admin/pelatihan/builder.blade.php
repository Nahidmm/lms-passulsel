@extends('layouts.app')

@section('title', 'Structure Builder: ' . $pelatihan->judul)

@push('styles')
<!-- SortableJS styling -->
<style>
    .sortable-ghost { opacity: 0.4; background-color: #f3f4f6; }
    .sortable-chosen { border-color: #0d9488; }
    .sortable-drag { cursor: grabbing !important; }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.pelatihan.index') }}" class="text-text-secondary hover:text-primary transition-colors tooltip" data-tip="Kembali ke Daftar">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                {{ $pelatihan->is_active ? 'Published' : 'Draft' }}
            </span>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">{{ $pelatihan->judul }}</h1>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.pelatihan.materi.create', $pelatihan->id) }}" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
            <i data-lucide="file-plus" class="w-4 h-4"></i> Tambah Materi
        </a>
        <a href="{{ route('admin.pelatihan.materi.create', ['pelatihan' => $pelatihan->id, 'jenis' => 'quiz']) }}" class="inline-flex items-center justify-center gap-2 bg-accent hover:bg-accent-hover text-[var(--text-primary)] font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
            <i data-lucide="help-circle" class="w-4 h-4"></i> Tambah Kuis
        </a>
        <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}" target="_blank" class="inline-flex items-center justify-center gap-2 bg-secondary border border-border hover:bg-secondary/70 text-text-primary font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
            <i data-lucide="external-link" class="w-4 h-4"></i> Preview
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Builder Canvas -->
    <div class="lg:col-span-2">
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border overflow-hidden">
            <div class="p-4 border-b border-border bg-secondary/30 flex items-center justify-between">
                <h3 class="font-bold text-text-primary flex items-center gap-2">
                    <i data-lucide="network" class="w-5 h-5 text-primary"></i> Kurikulum Pelatihan
                </h3>
                <span class="text-xs text-text-secondary">Drag-and-Drop untuk mengubah urutan</span>
            </div>
            
            <div class="p-4" id="kurikulum-builder">
                @if($pelatihan->materis->isEmpty())
                    <div class="text-center py-12 border-2 border-dashed border-border rounded-xl">
                        <i data-lucide="folder-open" class="w-12 h-12 text-border mx-auto mb-3"></i>
                        <h4 class="text-text-primary font-medium">Belum ada Materi</h4>
                        <p class="text-text-secondary text-sm mt-1 mb-4">Mulai bangun kurikulum dengan menambahkan materi pertama Anda.</p>
                        <a href="{{ route('admin.pelatihan.materi.create', $pelatihan->id) }}" class="inline-flex items-center justify-center gap-2 bg-primary/10 text-primary hover:bg-primary hover:text-[var(--text-primary)] font-medium px-4 py-2 rounded-lg transition-colors">
                            Tambah Materi Pertama
                        </a>
                    </div>
                @else
                    <!-- Materis List -->
                    <div id="materis-container" class="space-y-2 materis-container">
                        @foreach($pelatihan->materis as $materi)
                        <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm border border-border rounded-lg shadow-sm overflow-hidden" data-id="{{ $materi->id }}" data-type="materi">
                            <div class="flex items-center justify-between p-3 cursor-move group hover:bg-secondary/20 transition-colors">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="grip-vertical" class="w-5 h-5 text-text-secondary opacity-30 group-hover:opacity-100 transition-opacity"></i>
                                    
                                    @if($materi->jenis === 'quiz')
                                        <div class="w-8 h-8 rounded bg-accent/10 flex items-center justify-center text-accent shrink-0">
                                            <i data-lucide="help-circle" class="w-4 h-4"></i>
                                        </div>
                                    @elseif($materi->jenis === 'video_embed')
                                        <div class="w-8 h-8 rounded bg-red-500/10 flex items-center justify-center text-red-500 shrink-0">
                                            <i data-lucide="youtube" class="w-4 h-4"></i>
                                        </div>
                                    @elseif($materi->jenis === 'link')
                                        <div class="w-8 h-8 rounded bg-[#f0b429]/10 flex items-center justify-center text-[#c8891a] shrink-0">
                                            <i data-lucide="link" class="w-4 h-4"></i>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <p class="text-sm font-medium text-text-primary leading-tight">{{ $materi->urutan }}. {{ $materi->judul }}</p>
                                        <p class="text-xs text-text-secondary mt-0.5">
                                            @if($materi->jenis === 'quiz') {{ $materi->durasi_menit ?? 'Tanpa batas' }} Menit &bull; Kuis Evaluasi ({{ $materi->soals->count() }} Soal)
                                            @else {{ $materi->durasi_baca }} Menit &bull; {{ strtoupper($materi->jenis) }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if($materi->jenis === 'quiz')
                                        <a href="{{ route('admin.materi.edit', $materi->id) }}" class="p-2 text-xs text-text-secondary hover:text-accent hover:bg-accent/10 rounded transition-colors tooltip" data-tip="Kelola Soal & Konfigurasi">
                                            <i data-lucide="list-checks" class="w-4 h-4"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.materi.edit', $materi->id) }}" class="p-2 text-xs text-text-secondary hover:text-primary hover:bg-primary/10 rounded transition-colors tooltip" data-tip="Konfigurasi">
                                            <i data-lucide="settings" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.materi.destroy', $materi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus item ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-xs text-text-secondary hover:text-danger hover:bg-danger/10 rounded transition-colors tooltip" data-tip="Hapus">
                                            <i data-lucide="trash" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Info Panel -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm rounded-xl shadow-sm border border-border p-5">
            <h3 class="font-bold text-text-primary flex items-center gap-2 mb-4">
                <i data-lucide="info" class="w-5 h-5 text-primary"></i> Statistik Pelatihan
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-text-secondary">Total Materi / Video</span>
                    <span class="font-bold text-text-primary">{{ $pelatihan->materis->where('jenis', '!=', 'quiz')->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-text-secondary">Total Kuis Evaluasi</span>
                    <span class="font-bold text-text-primary">{{ $pelatihan->materis->where('jenis', 'quiz')->count() }}</span>
                </div>
            </div>
        </div>
        
        <div class="bg-secondary/30 rounded-xl border border-dashed border-border p-5">
            <h4 class="text-sm font-bold text-text-primary mb-2">Petunjuk Penggunaan:</h4>
            <ul class="text-xs text-text-secondary space-y-2 list-disc pl-4">
                <li>Klik tombol <strong>Tambah Materi</strong> untuk membuat materi bacaan/video baru.</li>
                <li>Gunakan <strong>Tambah Kuis</strong> untuk membuat kuis evaluasi.</li>
                <li>Klik dan seret (drag) ikon <i data-lucide="grip-vertical" class="w-3 h-3 inline"></i> untuk mengatur ulang urutan materi/kuis.</li>
                <li>Untuk mengatur konfigurasi materi, klik tombol <i data-lucide="settings" class="w-3 h-3 inline"></i>.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make Materis Sortable
    const materisContainer = document.getElementById('materis-container');
    if (materisContainer) {
        new Sortable(materisContainer, {
            animation: 150,
            handle: '.cursor-move',
            onEnd: function (evt) {
                // Here we would typically send an AJAX request to save the new order of materis
                console.log('Materi reordered');
            }
        });
    }
});
</script>
@endpush

