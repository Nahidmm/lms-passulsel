@extends('layouts.app')

@section('title', 'Modul Pembelajaran')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Modul Pembelajaran</h1>
    <p class="text-text-secondary mt-1">Daftar modul pembelajaran yang harus Anda selesaikan.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border p-6 mb-8">
    <div class="relative">
        <!-- Connecting Line -->
        <div class="absolute top-0 bottom-0 left-[2.25rem] w-0.5 bg-border z-0 hidden md:block"></div>

        <div class="space-y-6 relative z-10">
            @forelse($moduls as $index => $modul)
                @php
                    $status = $modulStatus[$modul->id]['status'];
                    $isLocked = $modulStatus[$modul->id]['is_locked'];
                @endphp

                <div class="flex flex-col md:flex-row gap-4 md:gap-6 group">
                    <!-- Status Icon -->
                    <div class="shrink-0 flex items-center justify-center w-12 md:w-[4.5rem]">
                        <div class="w-12 h-12 rounded-full border-4 flex items-center justify-center transition-colors
                            @if($isLocked) border-secondary bg-white text-border
                            @elseif($status === 'selesai') border-success bg-success/10 text-success
                            @else border-primary bg-primary/10 text-primary @endif">
                            
                            @if($isLocked)
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            @elseif($status === 'selesai')
                                <i data-lucide="check" class="w-6 h-6"></i>
                            @else
                                <i data-lucide="book-open" class="w-5 h-5"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Content Card -->
                    <div class="flex-1 bg-white rounded-xl border {{ $isLocked ? 'border-border bg-secondary/50' : 'border-border hover:border-primary/50 shadow-sm transition-shadow' }} p-5">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full uppercase tracking-wider
                                        @if($isLocked) bg-gray-200 text-gray-500
                                        @elseif($status === 'selesai') bg-success/10 text-success
                                        @else bg-primary/10 text-primary @endif">
                                        Modul {{ $modul->urutan }}
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-display font-bold {{ $isLocked ? 'text-text-secondary' : 'text-text-primary group-hover:text-primary transition-colors' }}">
                                    {{ $modul->judul }}
                                </h3>
                                
                                <p class="text-sm {{ $isLocked ? 'text-text-secondary/70' : 'text-text-secondary' }} mt-2 line-clamp-2">
                                    {{ $modul->deskripsi }}
                                </p>
                            </div>
                            
                            <div class="shrink-0 flex flex-col items-start md:items-end gap-3 mt-2 md:mt-0">
                                <div class="text-xs font-medium text-text-secondary flex items-center gap-1">
                                    <i data-lucide="layers" class="w-4 h-4"></i> {{ $modul->materis()->where('is_active', true)->count() }} Materi
                                </div>
                                
                                @if($isLocked)
                                    <button disabled class="w-full md:w-auto px-4 py-2 bg-secondary text-text-secondary font-medium rounded-lg cursor-not-allowed border border-border">
                                        Terkunci
                                    </button>
                                @else
                                    <a href="{{ route('peserta.pembelajaran.show', $modul->id) }}" class="w-full md:w-auto px-4 py-2 {{ $status === 'selesai' ? 'bg-white border border-success text-success hover:bg-success/5' : 'bg-primary hover:bg-primary-hover text-white shadow-md' }} font-bold rounded-lg text-center transition-colors">
                                        {{ $status === 'selesai' ? 'Lihat Kembali' : 'Mulai Belajar' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="book-x" class="w-8 h-8 text-text-secondary"></i>
                    </div>
                    <h3 class="text-lg font-bold text-text-primary">Belum Ada Modul</h3>
                    <p class="text-text-secondary mt-1">Admin belum menambahkan modul pembelajaran.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
