@extends('layouts.app')
@section('title', 'Learning Quests')

@section('content')
<div class="py-2 space-y-6">

    <!-- Page header -->
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-1 h-5 bg-gradient-to-b from-violet-500 to-cyan-500 rounded-full"></div>
                <span class="text-[10px] font-black text-violet-400 uppercase tracking-[0.2em]">Mission Map</span>
            </div>
            <h1 class="font-display font-black text-3xl text-[var(--text-primary)]">Learning Quests</h1>
            <p class="text-[var(--text-secondary)] mt-1 text-sm">Complete modules, earn XP, and climb the ranks.</p>
        </div>
    </div>

    <!-- Quest list -->
    <div class="relative">
        <!-- Vertical connector line -->
        <div class="absolute left-[2.75rem] top-14 bottom-14 w-0.5 bg-gradient-to-b from-violet-500/30 via-cyan-500/20 to-transparent hidden md:block pointer-events-none"></div>

        <div class="space-y-4">
            @forelse($moduls as $index => $modul)
                @php
                    $status   = $modulStatus[$modul->id]['status'];
                    $isLocked = $modulStatus[$modul->id]['is_locked'];
                    $total    = $modul->materis()->where('is_active', true)->count();
                    $xp       = $total * 50;
                @endphp

                <div class="flex flex-col md:flex-row gap-4 md:gap-6 group">
                    <!-- Icon column -->
                    <div class="shrink-0 flex items-center justify-center w-12 md:w-[5.5rem]">
                        <div class="relative w-14 h-14 rounded-2xl flex items-center justify-center border-2 transition-all duration-300 z-10
                            @if($isLocked)     border-slate-700    bg-slate-800/50   text-slate-600
                            @elseif($status==='selesai') border-emerald-500  bg-emerald-900/30  text-emerald-400 group-hover:glow-emerald
                            @else              border-cyan-500     bg-cyan-900/20    text-cyan-400    group-hover:glow-cyan @endif">

                            @if($isLocked)
                                <i data-lucide="lock" class="w-6 h-6"></i>
                            @elseif($status === 'selesai')
                                <i data-lucide="check-circle-2" class="w-7 h-7"></i>
                            @else
                                <i data-lucide="play-circle" class="w-7 h-7"></i>
                                <!-- pulse -->
                                <span class="absolute inset-0 rounded-2xl border-2 border-cyan-400 animate-ping opacity-30"></span>
                            @endif
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="flex-1 game-card {{ $isLocked ? 'opacity-60' : '' }} p-5 md:p-7">
                        <!-- Colour strip -->
                        <div class="absolute top-0 left-0 right-0 h-[2px] rounded-t-[1.25rem]
                            @if($isLocked)     bg-slate-700
                            @elseif($status==='selesai') bg-gradient-to-r from-emerald-500 to-teal-400
                            @else              bg-gradient-to-r from-violet-500 via-cyan-500 to-blue-500 @endif"></div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="text-[9px] font-black px-2 py-1 rounded-md uppercase tracking-widest border
                                        @if($isLocked)     bg-slate-800 text-[var(--text-muted)] border-slate-700
                                        @elseif($status==='selesai') bg-emerald-900/40 text-emerald-400 border-emerald-500/30
                                        @else              bg-violet-900/30 text-violet-300 border-violet-500/30 @endif">
                                        Stage {{ $modul->urutan }}
                                    </span>
                                    @if(!$isLocked)
                                        <span class="text-[9px] font-black text-amber-400 bg-amber-900/20 border border-amber-500/20 px-2 py-1 rounded-md flex items-center gap-1">
                                            <i data-lucide="zap" class="w-2.5 h-2.5"></i> +{{ $xp }} XP
                                        </span>
                                        <span class="text-[9px] font-black text-[var(--text-secondary)] bg-slate-800 border border-slate-700 px-2 py-1 rounded-md flex items-center gap-1">
                                            <i data-lucide="layers" class="w-2.5 h-2.5"></i> {{ $total }} missions
                                        </span>
                                    @endif
                                </div>

                                <h3 class="font-display font-black text-xl text-[var(--text-primary)] {{ !$isLocked ? 'group-hover:gradient-text-game transition-all' : '' }}">
                                    {{ $modul->judul }}
                                </h3>
                                <p class="text-sm text-[var(--text-secondary)] mt-2 line-clamp-2">{{ $modul->deskripsi }}</p>
                            </div>

                            <div class="shrink-0">
                                @if($isLocked)
                                    <button disabled class="px-5 py-2.5 bg-slate-800 text-[var(--text-muted)] font-black text-xs uppercase tracking-widest rounded-xl border border-slate-700 cursor-not-allowed flex items-center gap-2">
                                        <i data-lucide="lock" class="w-3.5 h-3.5"></i> Locked
                                    </button>
                                @elseif($status === 'selesai')
                                    <a href="{{ route('peserta.pembelajaran.show', $modul->id) }}" class="px-5 py-2.5 bg-emerald-900/40 text-emerald-400 font-black text-xs uppercase tracking-widest rounded-xl border border-emerald-500/40 hover:bg-emerald-900/60 transition-all flex items-center gap-2 glow-emerald">
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Replay
                                    </a>
                                @else
                                    <a href="{{ route('peserta.pembelajaran.show', $modul->id) }}" class="game-button">
                                        <i data-lucide="sword" class="w-4 h-4"></i> Start Quest
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="game-card p-16 text-center">
                    <i data-lucide="map-x" class="w-16 h-16 text-slate-700 mx-auto mb-4"></i>
                    <h3 class="font-display font-black text-xl text-[var(--text-primary)] mb-2">No Quests Available</h3>
                    <p class="text-[var(--text-muted)]">The quest board is empty. Check back later.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

