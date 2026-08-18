@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════
         HERO SECTION — Profile + Level
    ═══════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-border p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">

            {{-- Avatar + Greeting --}}
            <div class="flex items-center gap-5">
                <div class="relative shrink-0">
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
                         class="w-20 h-20 rounded-2xl object-cover ring-4 ring-border shadow">
                </div>
                <div>
                    <p class="text-xs font-bold text-text-secondary uppercase tracking-widest mb-1.5">
                        {{ Auth::user()->jabatan->nama_jabatan ?? 'Peserta' }}
                    </p>
                    <h1 class="text-2xl md:text-3xl font-display font-extrabold text-text-primary leading-tight">
                        Selamat Datang,<br>{{ explode(' ', Auth::user()->nama)[0] }}!
                    </h1>
                    <p class="text-sm text-text-secondary mt-1.5">
                        Pantau progres dan terus tingkatkan kompetensi Anda.
                    </p>
                </div>
            </div>

            {{-- Badge / Level Pill --}}
            <div class="flex items-center gap-3 bg-secondary rounded-2xl px-5 py-4 shrink-0 self-start sm:self-auto">
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center">
                    <i data-lucide="{{ Auth::user()->badge_icon }}" class="w-5 h-5 text-accent-hover"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-text-secondary uppercase tracking-widest leading-none">Level</p>
                    <p class="text-base font-extrabold text-text-primary leading-tight mt-0.5">{{ Auth::user()->badge }}</p>
                    <p class="text-sm font-bold text-primary mt-0.5">{{ number_format(Auth::user()->getTotalPoin()) }} Poin</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════
         STATS GRID — 4 Columns
    ═══════════════════════════════════ --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Materi --}}
        <div class="bg-white rounded-2xl border border-border p-5 flex flex-col gap-3 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-100 transition-colors flex items-center justify-center">
                <i data-lucide="book-open" class="w-5 h-5 text-blue-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Materi Selesai</p>
                <p class="text-3xl font-display font-extrabold text-text-primary leading-none">
                    {{ $materiSelesai }}<span class="text-base font-semibold text-text-secondary ml-1">/ {{ $totalMateri }}</span>
                </p>
            </div>
        </div>

        {{-- Evaluasi --}}
        <div class="bg-white rounded-2xl border border-border p-5 flex flex-col gap-3 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="w-10 h-10 rounded-xl bg-amber-50 group-hover:bg-amber-100 transition-colors flex items-center justify-center">
                <i data-lucide="clipboard-check" class="w-5 h-5 text-amber-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Rata-rata Kuis</p>
                <p class="text-3xl font-display font-extrabold text-text-primary leading-none">{{ $rataNilai }}</p>
            </div>
        </div>

        {{-- Peringkat --}}
        <div class="bg-white rounded-2xl border border-border p-5 flex flex-col gap-3 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 transition-colors flex items-center justify-center">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-emerald-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">Peringkat</p>
                <p class="text-3xl font-display font-extrabold text-text-primary leading-none">
                    #{{ $userRank }}<span class="text-base font-semibold text-text-secondary ml-1">/ {{ count($leaderboard) > 0 ? \App\Models\User::where('role', 'peserta')->count() : 0 }}</span>
                </p>
            </div>
        </div>

        {{-- Progres --}}
        <div class="bg-white rounded-2xl border border-border p-5 flex flex-col gap-3 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5 text-primary"></i>
                </div>
                <span class="text-2xl font-extrabold text-primary">{{ $progres }}%</span>
            </div>
            <div>
                <p class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Progres Belajar</p>
                <div class="w-full bg-secondary rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full bg-primary transition-all duration-700" style="width: {{ $progres }}%"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════
         MAIN CONTENT — CTA + Leaderboard
    ═══════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- LEFT: CTAs --}}
        <div class="lg:col-span-3 flex flex-col gap-5">

            {{-- Pelatihan Card --}}
            <div class="bg-white rounded-2xl border border-border hover:shadow-md transition-all duration-200">
                <div class="p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-1.5 text-[10px] font-extrabold text-primary bg-primary/5 border border-primary/15 rounded-lg px-2.5 py-1 uppercase tracking-widest mb-3">
                            <i data-lucide="book-open" class="w-3 h-3"></i> Pembelajaran
                        </div>
                        <h2 class="text-xl font-display font-extrabold text-text-primary mb-2">Katalog Pelatihan</h2>
                        <p class="text-sm text-text-secondary leading-relaxed max-w-xs">
                            Kerjakan pelatihan dan kuis, kumpulkan poin, serta naiki papan peringkat!
                        </p>
                        <div class="mt-4">
                            @if(Auth::user()->getActivePelatihan())
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-primary">
                                    <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                                    Pelatihan sedang aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-success">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    Siap memulai pelatihan baru
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('peserta.pelatihan.index') }}"
                       class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white font-bold py-3 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 text-sm shrink-0 whitespace-nowrap">
                        Buka Katalog
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            {{-- AI Assistant Card --}}
            @if(!Auth::user()->hasActiveSesiEvaluasi())
            <div class="bg-white rounded-2xl border border-border hover:shadow-md transition-all duration-200">
                <div class="p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-12 h-12 rounded-2xl bg-accent/10 flex items-center justify-center shrink-0 mt-0.5">
                            <i data-lucide="bot" class="w-6 h-6 text-accent-hover"></i>
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-[10px] font-extrabold text-accent-hover bg-accent/5 border border-accent/15 rounded-lg px-2.5 py-1 uppercase tracking-widest mb-2">
                                <i data-lucide="sparkles" class="w-3 h-3"></i> AI Assistant
                            </div>
                            <h2 class="text-lg font-display font-extrabold text-text-primary mb-1">Asisten AI Cerdas</h2>
                            <p class="text-sm text-text-secondary">Tanya regulasi, SOP, dan peraturan pemasyarakatan kapan saja.</p>
                        </div>
                    </div>
                    <a href="{{ route('ai.index') }}"
                       class="inline-flex items-center gap-2 bg-secondary hover:bg-border text-text-primary font-bold py-3 px-6 rounded-xl transition-all duration-200 text-sm shrink-0 whitespace-nowrap border border-border">
                        Mulai Chat
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT: Leaderboard --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-border h-full flex flex-col overflow-hidden">

                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="trophy" class="w-4.5 h-4.5 text-accent-hover"></i>
                        <h3 class="font-display font-extrabold text-text-primary">Papan Peringkat</h3>
                    </div>
                    <span class="text-xs font-bold text-text-secondary bg-secondary px-2.5 py-1 rounded-lg">Top 5</span>
                </div>

                <ul class="flex-1 divide-y divide-border/60">
                    @forelse($leaderboard as $i => $u)
                        @php
                            $isMe = $u->id === Auth::id();
                            $medals = ['🥇','🥈','🥉'];
                        @endphp
                        <li class="px-5 py-4 flex items-center gap-3 {{ $isMe ? 'bg-primary/5' : 'hover:bg-secondary/50' }} transition-colors">
                            <span class="text-xl shrink-0 w-8 text-center">
                                {{ $medals[$i] ?? '<span class="text-sm font-bold text-text-secondary">#'.($i+1).'</span>' }}
                            </span>
                            <img src="{{ $u->avatar_url }}" alt="{{ $u->nama }}"
                                 class="w-10 h-10 rounded-xl object-cover border-2 {{ $isMe ? 'border-primary/30' : 'border-border' }} shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-text-primary truncate flex items-center gap-1.5">
                                    {{ explode(' ', $u->nama)[0] }}
                                    @if($isMe)
                                        <span class="text-[9px] font-extrabold bg-primary text-white px-1.5 py-0.5 rounded uppercase tracking-wide">Anda</span>
                                    @endif
                                </p>
                                <p class="text-xs text-text-secondary truncate">{{ explode(' / ', $u->badge)[1] ?? 'Pemula' }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-extrabold text-text-primary">{{ number_format($u->total_poin) }}</p>
                                <p class="text-[10px] text-text-secondary font-semibold uppercase tracking-wide">poin</p>
                            </div>
                        </li>
                    @empty
                        <li class="py-12 flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center mb-3">
                                <i data-lucide="users" class="w-5 h-5 text-text-secondary"></i>
                            </div>
                            <p class="text-sm font-medium text-text-secondary">Belum ada data.</p>
                        </li>
                    @endforelse
                </ul>

            </div>
        </div>

    </div>

</div>
@endsection
