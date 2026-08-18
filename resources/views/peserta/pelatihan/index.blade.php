@extends('layouts.app')

@section('title', 'Katalog Pelatihan')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-text-primary">Katalog Pelatihan</h1>
    <p class="text-text-secondary mt-1">Pilih dan ikuti pelatihan yang tersedia. Anda hanya dapat mengambil satu pelatihan aktif pada satu waktu.</p>
</div>

<!-- Search & Filter Bar -->
<div class="mb-6">
    <form action="{{ route('peserta.pelatihan.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 bg-white p-4 rounded-xl shadow-sm border border-border">
        <!-- Search -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="w-5 h-5 text-text-secondary"></i>
            </div>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari judul pelatihan atau deskripsi..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-secondary/30 border border-border rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-text-primary transition-all">
        </div>
        
        <!-- Filter -->
        <div class="sm:w-48 shrink-0 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="filter" class="w-4 h-4 text-text-secondary"></i>
            </div>
            <select name="status" class="w-full pl-9 pr-8 py-2.5 bg-secondary/30 border border-border rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-text-primary appearance-none transition-all cursor-pointer" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="belum" {{ ($status ?? '') === 'belum' ? 'selected' : '' }}>Belum Mulai</option>
                <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                <option value="selesai" {{ ($status ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="terkunci" {{ ($status ?? '') === 'terkunci' ? 'selected' : '' }}>Terkunci</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <i data-lucide="chevron-down" class="w-4 h-4 text-text-secondary"></i>
            </div>
        </div>

        @if(request()->has('search') || request()->has('status'))
            <a href="{{ route('peserta.pelatihan.index') }}" class="flex items-center justify-center px-4 py-2.5 bg-secondary hover:bg-border text-text-secondary rounded-lg font-medium transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </a>
        @endif
        
        <button type="submit" class="hidden">Cari</button>
    </form>
</div>

@if(session('error'))
<div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
    <i data-lucide="alert-circle" class="w-5 h-5"></i>
    <span class="font-medium text-sm">{{ session('error') }}</span>
</div>
@endif

@if(session('info'))
<div class="bg-primary/10 border border-primary/20 text-primary px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
    <i data-lucide="info" class="w-5 h-5"></i>
    <span class="font-medium text-sm">{{ session('info') }}</span>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($pelatihans as $pelatihan)
        @php
            $progres = \App\Models\ProgresPelatihan::where('user_id', auth()->id())
                        ->where('pelatihan_id', $pelatihan->id)->first();
            $status = $progres ? $progres->status : 'belum';
            
            // Is Locked? (User has an active course, and it's not this one, AND this one is not finished)
            $isLocked = $activePelatihan && $activePelatihan->pelatihan_id !== $pelatihan->id && $status !== 'selesai';
        @endphp

        <div class="bg-white rounded-xl border border-border overflow-hidden {{ $isLocked ? 'opacity-75 grayscale-[50%]' : 'hover:shadow-lg transition-all group hover:-translate-y-1 duration-300' }} flex flex-col h-full relative">
            
            @if($isLocked)
                <div class="absolute inset-0 bg-secondary/40 z-10 flex items-center justify-center backdrop-blur-[1px]">
                    <div class="bg-white/90 px-4 py-2 rounded-lg shadow-sm border border-border flex items-center gap-2 text-danger font-bold text-sm">
                        <i data-lucide="lock" class="w-4 h-4"></i> Terkunci
                    </div>
                </div>
            @endif

            <!-- Thumbnail -->
            <div class="h-44 bg-secondary relative overflow-hidden shrink-0">
                <img src="{{ $pelatihan->gambar_thumbnail ? asset('storage/'.$pelatihan->gambar_thumbnail) : 'https://picsum.photos/seed/'.$pelatihan->id.'/400/250' }}" alt="Cover" class="w-full h-full object-cover {{ !$isLocked ? 'group-hover:scale-105 transition-transform duration-500' : '' }}">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80"></div>
                
                <div class="absolute bottom-3 left-4">
                    @if($status === 'selesai')
                        <span class="bg-success text-white px-2.5 py-1 rounded shadow-sm text-xs font-bold uppercase flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Selesai
                        </span>
                    @elseif($status === 'aktif')
                        <span class="bg-primary text-white px-2.5 py-1 rounded shadow-sm text-xs font-bold uppercase flex items-center gap-1.5">
                            <i data-lucide="play-circle" class="w-3.5 h-3.5"></i> Sedang Dikerjakan
                        </span>
                    @else
                        <span class="bg-white/90 text-text-primary px-2.5 py-1 rounded shadow-sm text-[10px] font-bold tracking-wide uppercase">
                            Tersedia
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="p-5 flex flex-col flex-grow relative z-0">
                <h3 class="text-base font-bold text-text-primary {{ !$isLocked ? 'group-hover:text-primary transition-colors' : '' }} line-clamp-2 leading-tight mb-2">
                    {{ $pelatihan->judul }}
                </h3>
                
                <p class="text-xs text-text-secondary line-clamp-2 mb-4">{{ $pelatihan->deskripsi }}</p>
                
                <div class="mt-auto flex items-center gap-4 text-xs font-medium text-text-secondary">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        {{ $pelatihan->materis->count() }} Materi
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-5 py-3 border-t border-border flex items-center justify-between mt-auto shrink-0 bg-gray-50/50 relative z-0">
                @if($status === 'selesai' || $status === 'aktif')
                    <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}" class="w-full text-center bg-white border border-primary text-primary hover:bg-primary/5 font-bold py-2 rounded-lg text-sm transition-colors">
                        {{ $status === 'aktif' ? 'Lanjutkan Belajar' : 'Lihat Kembali' }}
                    </a>
                @elseif(!$isLocked)
                    <button type="button" onclick="openEnrollModal({{ $pelatihan->id }}, '{{ addslashes($pelatihan->judul) }}')" class="w-full text-center bg-primary hover:bg-primary-hover text-white font-bold py-2 rounded-lg text-sm transition-colors shadow-sm">
                        Mulai Pelatihan
                    </button>
                @else
                    <button type="button" disabled class="w-full text-center bg-secondary text-text-secondary font-bold py-2 rounded-lg text-sm cursor-not-allowed">
                        Terkunci
                    </button>
                @endif
            </div>
        </div>
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

{{-- Enroll Confirmation Modal --}}
<div id="enrollModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="enrollModalContent">
        <div class="p-6">
            <div class="w-12 h-12 rounded-full bg-warning/10 text-warning flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-xl font-bold text-text-primary text-center mb-2">Mulai Pelatihan?</h3>
            <p class="text-sm text-text-secondary text-center mb-6">
                Anda akan memulai pelatihan <strong id="modalPelatihanJudul" class="text-text-primary"></strong>. <br><br>
                <span class="text-danger font-medium">Penting:</span> Pelatihan lain akan <strong>terkunci</strong> hingga Anda berhasil menyelesaikan semua materi pada pelatihan ini.
            </p>

            <form id="enrollForm" method="POST" action="">
                @csrf
                <div class="flex gap-3">
                    <button type="button" onclick="closeEnrollModal()" class="flex-1 py-2.5 bg-secondary hover:bg-border text-text-primary font-bold rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl transition-colors shadow-sm">
                        Ya, Mulai Pelatihan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openEnrollModal(id, judul) {
        document.getElementById('modalPelatihanJudul').textContent = judul;
        document.getElementById('enrollForm').action = `/peserta/pelatihan/${id}/enroll`;
        
        const modal = document.getElementById('enrollModal');
        const content = document.getElementById('enrollModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEnrollModal() {
        const modal = document.getElementById('enrollModal');
        const content = document.getElementById('enrollModalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
