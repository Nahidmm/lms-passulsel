@extends('layouts.app')
@section('title', 'Katalog Pelatihan')

@section('content')
<div class="space-y-6 py-1">

    {{-- Page heading --}}
    <div>
        <div class="section-label">
            <div class="line"></div>
            <span>Katalog Pelatihan</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-[var(--text-primary)]">Pilih Pelatihanmu</h1>
        <p class="text-[var(--text-secondary)] mt-1 text-sm">Kamu hanya dapat mengambil satu pelatihan aktif dalam satu waktu.</p>
    </div>

    {{-- Search & filter --}}
    <form action="{{ route('peserta.pelatihan.index') }}" method="GET">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-[var(--text-muted)] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari judul pelatihan..."
                    class="form-input pl-10">
            </div>
            <div class="relative sm:w-52 shrink-0">
                <i data-lucide="filter" class="w-4 h-4 text-[var(--text-muted)] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <select name="status" class="form-input pl-10 appearance-none cursor-pointer pr-9" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="belum"   {{ ($status ?? '') === 'belum'    ? 'selected' : '' }}>Belum Mulai</option>
                    <option value="aktif"   {{ ($status ?? '') === 'aktif'    ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="selesai" {{ ($status ?? '') === 'selesai'  ? 'selected' : '' }}>Selesai</option>
                    <option value="terkunci"{{ ($status ?? '') === 'terkunci' ? 'selected' : '' }}>Terkunci</option>
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 text-[var(--text-muted)] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('peserta.pelatihan.index') }}" class="btn btn-ghost sm:w-auto flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i> Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($pelatihans as $pelatihan)
            @php
                $progres  = \App\Models\ProgresPelatihan::where('user_id', auth()->user()->id)->where('pelatihan_id', $pelatihan->id)->first();
                $status   = $progres ? $progres->status : 'belum';
                $isLocked = $activePelatihan && $activePelatihan->pelatihan_id !== $pelatihan->id && $status !== 'selesai';
            @endphp

            <div class="game-card flex flex-col h-full {{ $isLocked ? 'opacity-70' : 'game-card-interactive' }}">
                {{-- Thumbnail --}}
                <div class="relative h-44 overflow-hidden rounded-t-[1rem]">
                    <img src="{{ $pelatihan->gambar_thumbnail ? asset('storage/'.$pelatihan->gambar_thumbnail) : 'https://picsum.photos/seed/'.$pelatihan->id.'/400/250' }}"
                         alt="Cover {{ $pelatihan->judul }}"
                         class="w-full h-full object-cover {{ !$isLocked ? 'group-hover:scale-105 transition-transform duration-500' : '' }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>

                    {{-- Status badge on image --}}
                    <div class="absolute bottom-3 left-4">
                        @if($isLocked)
                            <span class="badge badge-rose"><i data-lucide="lock" class="w-2.5 h-2.5"></i> Terkunci</span>
                        @elseif($status === 'selesai')
                            <span class="badge badge-emerald"><i data-lucide="check-circle" class="w-2.5 h-2.5"></i> Selesai</span>
                        @elseif($status === 'aktif')
                            <span class="badge badge-violet"><i data-lucide="play-circle" class="w-2.5 h-2.5"></i> Berlangsung</span>
                        @else
                            <span class="badge badge-cyan"><i data-lucide="sparkles" class="w-2.5 h-2.5"></i> Tersedia</span>
                        @endif
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-bold text-base text-[var(--text-primary)] leading-snug mb-2 line-clamp-2">{{ $pelatihan->judul }}</h3>
                    <p class="text-sm text-[var(--text-secondary)] line-clamp-2 leading-relaxed flex-1">{{ $pelatihan->deskripsi }}</p>

                    <div class="flex items-center gap-3 mt-3 text-xs text-[var(--text-muted)] font-semibold">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-violet-400"></i>
                            {{ $pelatihan->materis->count() }} Materi
                        </span>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="px-5 pb-5">
                    @if($status === 'selesai' || $status === 'aktif')
                        <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}" class="btn btn-primary w-full justify-center">
                            {{ $status === 'aktif' ? 'Lanjutkan Belajar' : 'Lihat Kembali' }}
                        </a>
                    @elseif(!$isLocked)
                        <a href="{{ route('peserta.pelatihan.show', $pelatihan->id) }}"
                            class="btn btn-primary w-full justify-center">
                            Mulai Pelatihan
                        </a>
                    @else
                        <button type="button" disabled class="btn btn-ghost w-full justify-center cursor-not-allowed opacity-60">
                            <i data-lucide="lock" class="w-4 h-4"></i> Terkunci
                        </button>
                    @endif
                </div>
            </div>

        @empty
            <div class="col-span-full">
                <div class="game-card p-14 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-700 mx-auto mb-4"></i>
                    <h3 class="text-lg font-bold text-[var(--text-primary)] mb-1">Belum Ada Pelatihan</h3>
                    <p class="text-[var(--text-muted)] text-sm">Saat ini belum ada pelatihan aktif yang tersedia.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($pelatihans->hasPages())
        <div class="flex justify-center">{{ $pelatihans->links() }}</div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// No modal needed - enrollment happens automatically on first visit
</script>
@endpush

