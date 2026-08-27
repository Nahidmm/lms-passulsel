<header class="app-header">

    {{-- Left: Hamburger + brand --}}
    <div class="flex items-center gap-3 flex-1 min-w-0">
        {{-- Sidebar toggle (desktop: collapse/expand | mobile: drawer) --}}
        <button onclick="toggleSidebar()"
            id="sidebar-toggle-btn"
            class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-all"
            style="color:var(--text-secondary);border:1px solid rgba(255,255,255,0.07);"
            aria-label="Toggle sidebar"
            onmouseenter="this.style.color='var(--paper)';this.style.background='rgba(255,255,255,0.07)'"
            onmouseleave="this.style.color='var(--text-secondary)';this.style.background='transparent'">
            <i data-lucide="panel-left" class="w-4 h-4"></i>
        </button>

        {{-- Page title / brand on mobile --}}
        <div class="md:hidden flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-violet-600 to-blue-500 flex items-center justify-center">
                <i data-lucide="zap" class="w-3 h-3 text-white"></i>
            </div>
            <span class="font-black text-[var(--text-primary)] text-sm">SPEKTRA</span>
        </div>
    </div>

    {{-- Right: XP chips (desktop) + notif + user --}}
    <div class="flex items-center gap-2 ml-auto">

        {{-- Peserta XP chips â€” desktop only --}}
        @if(Auth::user()->isPeserta())
        <div class="hidden lg:flex items-center gap-2">
            <span class="badge badge-violet text-xs">
                <i data-lucide="zap" class="w-3 h-3"></i> Lvl 12
            </span>
            <span class="badge badge-amber text-xs">
                <i data-lucide="flame" class="w-3 h-3"></i> 5 Hari
            </span>
            <span class="badge badge-emerald text-xs">
                <i data-lucide="star" class="w-3 h-3"></i>
                {{ number_format(Auth::user()->getTotalPoin()) }} XP
            </span>
        </div>
        @endif

        {{-- Theme Toggle --}}
        <button id="theme-toggle" class="relative w-9 h-9 flex items-center justify-center rounded-xl text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-[var(--card)] border border-transparent transition-all mr-1">
            <i id="theme-toggle-dark-icon" data-lucide="moon" class="w-4 h-4 hidden"></i>
            <i id="theme-toggle-light-icon" data-lucide="sun" class="w-4 h-4 hidden"></i>
        </button>

        {{-- Notification bell --}}
        @php
            $unreadNotifs = \App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->orderByDesc('created_at')->get();
            $recentNotifs = \App\Models\Notification::where('user_id', Auth::id())->orderByDesc('created_at')->take(6)->get();
        @endphp

        <div class="relative group">
            <button class="relative w-9 h-9 flex items-center justify-center rounded-xl text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-[var(--card)] border border-[var(--border)] shadow-sm border border-transparent transition-all">
                <i data-lucide="bell" class="w-4 h-4"></i>
                @if($unreadNotifs->count() > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-[#0d1117] animate-pulse"></span>
                @endif
            </button>

            {{-- Dropdown --}}
            <div class="notif-dropdown">
                <div class="flex items-center justify-between p-4 border-b border-[var(--border)]">
                    <h3 class="text-sm font-bold text-[var(--text-primary)]">Notifikasi</h3>
                    @if($unreadNotifs->count() > 0)
                        <form action="{{ route('notifikasi.read-all') }}" method="POST">
                            @csrf
                            <button class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold">Tandai semua</button>
                        </form>
                    @endif
                </div>
                <div class="max-h-64 overflow-y-auto">
                    @forelse($recentNotifs as $notif)
                        <a href="{{ route('notifikasi.read', $notif->id) }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-[var(--card)] border border-[var(--border)] shadow-sm transition-colors border-b last:border-0 {{ !$notif->isRead() ? 'bg-[var(--card-hover)]' : '' }}">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5
                                {{ $notif->type==='success' ? 'bg-emerald-500/15 text-emerald-400' :
                                   ($notif->type==='warning' ? 'bg-amber-500/15 text-amber-400'  :
                                   ($notif->type==='danger'  ? 'bg-rose-500/15 text-rose-400'    :
                                                               'bg-violet-500/15 text-violet-400')) }}">
                                <i data-lucide="{{ $notif->icon ?? 'bell' }}" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[var(--text-primary)] truncate">{{ $notif->title }}</p>
                                @if($notif->body)
                                    <p class="text-xs text-[var(--text-secondary)] line-clamp-2 mt-0.5">{{ $notif->body }}</p>
                                @endif
                                <p class="text-[10px] text-[var(--text-muted)] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notif->isRead())
                                <span class="w-2 h-2 rounded-full bg-violet-400 shrink-0 mt-2"></span>
                            @endif
                        </a>
                    @empty
                        <div class="py-10 text-center">
                            <i data-lucide="bell-off" class="w-8 h-8 text-[var(--text-muted)] mx-auto mb-2"></i>
                            <p class="text-sm text-[var(--text-secondary)]">Belum ada notifikasi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- User avatar + dropdown --}}
        <div class="relative group">
            <button class="flex items-center gap-2 p-1 rounded-xl hover:bg-[var(--card)] border border-[var(--border)] shadow-sm border border-transparent transition-all">
                <img src="{{ Auth::user()->avatar_url }}"
                     class="w-8 h-8 rounded-xl object-cover" alt="Avatar"
                     style="border:2px solid rgba(200,137,26,0.35);">
                <i data-lucide="chevron-down" class="w-3 h-3 hidden sm:block" style="color:var(--text-muted);"></i>
            </button>

            <div class="user-dropdown">
                <div class="p-4 border-b border-[var(--border)]">
                    <p class="text-sm font-bold text-[var(--text-primary)] truncate">{{ Auth::user()->nama }}</p>
                    <p class="text-xs text-[var(--text-secondary)] mt-0.5 truncate">{{ Auth::user()->nip }}</p>
                </div>
                <div class="p-2">
                    <a href="{{ route('profil.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--card)] border border-[var(--border)] shadow-sm font-semibold transition-all">
                        <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm text-slate-400 hover:text-rose-400 hover:bg-rose-500/8 font-semibold transition-all">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>

