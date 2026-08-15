@extends('layouts.app')

@section('title', 'Katalog Pelatihan')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-text-primary">Katalog Pelatihan</h1>
    
    <!-- Tab Navigation -->
    <div class="flex gap-6 mt-6 border-b border-border">
        <button class="pb-3 border-b-2 border-primary text-primary font-medium text-sm">Terbuka ({{ $pelatihans->total() ?? $pelatihans->count() }})</button>
        <button class="pb-3 text-text-secondary hover:text-text-primary font-medium text-sm transition-colors">Sedang Berjalan (0)</button>
        <button class="pb-3 text-text-secondary hover:text-text-primary font-medium text-sm transition-colors">Selesai (0)</button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($pelatihans as $pelatihan)
        <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}" class="bg-white rounded-xl border border-border overflow-hidden hover:shadow-lg transition-all group flex flex-col h-full transform hover:-translate-y-1 duration-300">
            <!-- Thumbnail -->
            <div class="h-44 bg-secondary relative overflow-hidden shrink-0">
                <img src="https://picsum.photos/seed/{{ $pelatihan->id + 100 }}/400/250" alt="Cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            
            <!-- Card Body -->
            <div class="p-5 flex flex-col flex-grow">
                <div class="text-xs text-text-secondary mb-2 flex justify-between items-center">
                    <span>{{ $pelatihan->created_at->format('M d, Y') }}</span>
                    <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase">Terbuka</span>
                </div>
                
                <h3 class="text-base font-bold text-text-primary group-hover:text-primary transition-colors line-clamp-2 leading-tight mb-4">
                    {{ $pelatihan->judul }}
                </h3>
                
                <div class="mt-auto flex items-center gap-4 text-xs font-medium text-text-secondary">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        {{ $pelatihan->materis->sum('durasi_baca') + $pelatihan->materis->sum('durasi_menit') }} mnt
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        {{ $pelatihan->materis->count() }} Materi
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-5 py-3 border-t border-border flex items-center justify-between mt-auto shrink-0 bg-gray-50/50">
                <span class="text-xs font-medium text-text-secondary">
                    Status: <strong class="text-text-primary">Free</strong>
                </span>
                <button class="text-text-secondary hover:text-primary p-1 rounded-full hover:bg-primary/10 transition-colors">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
        </a>
    @empty
        <div class="col-span-full py-16 text-center bg-white border border-border rounded-xl">
            <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="w-8 h-8 text-text-secondary"></i>
            </div>
            <h3 class="text-lg font-bold text-text-primary">Belum Ada Pelatihan</h3>
            <p class="text-text-secondary mt-1">Saat ini belum ada pelatihan aktif yang tersedia.</p>
        </div>
    @endforelse
</div>

@if($pelatihans->hasPages())
<div class="mt-8 flex justify-center">
    {{ $pelatihans->links() }}
</div>
@endif
@endsection
