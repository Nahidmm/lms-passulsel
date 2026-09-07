@extends('layouts.app')
@section('title', 'Katalog Pelatihan')

@section('content')
<div class="space-y-6">

    {{-- Page heading --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Katalog Pelatihan</h1>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Pilih program pelatihan pembinaan disiplin ASN yang tersedia.</p>
        </div>
    </div>

    {{-- Search & filter --}}
    <form action="{{ route('peserta.pelatihan.index') }}" method="GET">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari judul pelatihan..."
                    class="w-full pl-10 pr-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]">
            </div>
            <div class="relative sm:w-52 shrink-0">
                <i data-lucide="filter" class="w-4 h-4 text-[var(--text-muted)] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <select name="status" class="w-full pl-10 pr-9 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all appearance-none cursor-pointer" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="belum"   {{ ($status ?? '') === 'belum'    ? 'selected' : '' }}>Belum Mulai</option>
                    <option value="aktif"   {{ ($status ?? '') === 'aktif'    ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="selesai" {{ ($status ?? '') === 'selesai'  ? 'selected' : '' }}>Selesai</option>
                    <option value="terkunci"{{ ($status ?? '') === 'terkunci' ? 'selected' : '' }}>Terkunci</option>
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 text-[var(--text-muted)] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('peserta.pelatihan.index') }}" class="btn btn-secondary text-[var(--text-secondary)] sm:w-auto flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm">
                    <i data-lucide="x" class="w-4 h-4"></i> Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($pelatihans as $pelatihan)
            @php
                $progres  = \App\Models\ProgresPelatihan::where('user_id', auth()->user()->id)->where('pelatihan_id', $pelatihan->id)->first();
                $status   = $progres ? $progres->status : 'belum';
                $isLocked = $activePelatihan && $activePelatihan->pelatihan_id !== $pelatihan->id && $status !== 'selesai';
            @endphp

            <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl overflow-hidden shadow-xs hover:border-primary/40 transition-all flex flex-col h-full group {{ $isLocked ? 'opacity-65' : '' }}">
                {{-- Thumbnail --}}
                <div class="relative h-44 overflow-hidden bg-[var(--muted)]">
                    <img src="{{ $pelatihan->gambar_thumbnail ? asset('storage/'.$pelatihan->gambar_thumbnail) : 'https://picsum.photos/seed/'.$pelatihan->id.'/400/250' }}"
                         alt="Cover {{ $pelatihan->judul }}"
                         class="w-full h-full object-cover {{ !$isLocked ? 'group-hover:scale-105 transition-transform duration-500' : '' }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                    {{-- Status badge on image --}}
                    <div class="absolute bottom-3 left-3">
                        @if($isLocked)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/90 text-white shadow-xs backdrop-blur-xs">
                                <i data-lucide="lock" class="w-3 h-3"></i> Terkunci
                            </span>
                        @elseif($status === 'selesai')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/90 text-white shadow-xs backdrop-blur-xs">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> Selesai
                            </span>
                        @elseif($status === 'aktif')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/90 text-white shadow-xs backdrop-blur-xs">
                                <i data-lucide="play-circle" class="w-3 h-3"></i> Berlangsung
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-500/90 text-white shadow-xs backdrop-blur-xs">
                                <i data-lucide="book-open" class="w-3 h-3"></i> Tersedia
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-bold text-base text-[var(--text-primary)] leading-snug mb-2 line-clamp-2">{{ $pelatihan->judul }}</h3>
                    <p class="text-sm text-[var(--text-secondary)] line-clamp-2 leading-relaxed flex-1">{{ $pelatihan->deskripsi }}</p>

                    <div class="flex items-center gap-3 mt-4 pt-3 border-t border-[var(--border)] text-xs text-[var(--text-secondary)] font-medium">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-primary"></i>
                            {{ $pelatihan->materis->count() }} Materi Pelajaran
                        </span>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="px-5 pb-5">
                    @if($status === 'selesai' || $status === 'aktif')
                        <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}" class="btn btn-primary text-white w-full justify-center py-2.5 rounded-xl font-medium shadow-xs transition-all flex items-center gap-2">
                            <span>{{ $status === 'aktif' ? 'Lanjutkan Belajar' : 'Buka Materi' }}</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @elseif(!$isLocked)
                        <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}"
                            class="btn btn-primary text-white w-full justify-center py-2.5 rounded-xl font-medium shadow-xs transition-all flex items-center gap-2">
                            <span>Mulai Pelatihan</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @else
                        <button type="button" disabled class="btn btn-secondary text-[var(--text-muted)] w-full justify-center py-2.5 rounded-xl font-medium cursor-not-allowed flex items-center gap-2">
                            <i data-lucide="lock" class="w-4 h-4"></i> Terkunci
                        </button>
                    @endif
                </div>
            </div>

        @empty
            <div class="col-span-full">
                <div class="bg-[var(--card)] border border-[var(--border)] rounded-2xl p-16 text-center shadow-xs">
                    <i data-lucide="inbox" class="w-12 h-12 text-[var(--text-muted)] mx-auto mb-3"></i>
                    <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">Belum Ada Pelatihan</h3>
                    <p class="text-[var(--text-secondary)] text-sm">Saat ini belum ada pelatihan aktif yang tersedia.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($pelatihans->hasPages())
        <div class="flex justify-center pt-4">{{ $pelatihans->links() }}</div>
    @endif

</div>
@endsection
