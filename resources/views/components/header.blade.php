<header class="app-header">

    {{-- Left: Sidebar Toggle + Title / Brand --}}
    <div class="flex items-center gap-3 flex-1 min-w-0">
        {{-- Sidebar toggle (desktop: collapse/expand | mobile: drawer) --}}
        <button onclick="toggleSidebar()"
            id="sidebar-toggle-btn"
            class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--card-hover)] border border-[var(--border)] transition-colors shadow-xs"
            aria-label="Toggle sidebar">
            <i data-lucide="panel-left" class="w-4 h-4"></i>
        </button>

        {{-- Mobile Brand --}}
        <div class="md:hidden flex items-center gap-2 min-w-0">
            <img src="{{ asset('logo/strapsuspas.png') }}" alt="STRAPSUSPAS" class="w-7 h-7 object-contain shrink-0">
            <span class="font-bold text-[var(--text-primary)] text-sm tracking-tight truncate">STRAPSUSPAS</span>
        </div>

        {{-- Desktop Page Context / Organization Badge --}}
        <div class="hidden md:flex items-center gap-2 min-w-0">
            <span class="text-xs font-semibold text-[var(--text-secondary)]">Kanwil Ditjenpas Sulsel</span>
            <span class="text-[var(--border)]">•</span>
            <span class="text-xs text-[var(--text-muted)] font-medium">Petugas Paten, Pembinaan Pasti, Pemasyarakatan Berdampak</span>
        </div>
    </div>

    {{-- Right: Actions, Theme, Notifications, Profile --}}
    <div class="flex items-center gap-2 ml-auto">

        {{-- Theme Toggle --}}
        <button id="theme-toggle" class="w-9 h-9 flex items-center justify-center rounded-lg text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--card-hover)] border border-[var(--border)] transition-colors shadow-xs" title="Ganti Tema">
            <i id="theme-toggle-dark-icon" data-lucide="moon" class="w-4 h-4 hidden"></i>
            <i id="theme-toggle-light-icon" data-lucide="sun" class="w-4 h-4 hidden"></i>
        </button>

        {{-- Notification bell --}}
        @php
            $unreadNotifs = \App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->orderByDesc('created_at')->get();
            $recentNotifs = \App\Models\Notification::where('user_id', Auth::id())->orderByDesc('created_at')->take(6)->get();
        @endphp

        <div class="relative group">
            <button class="relative w-9 h-9 flex items-center justify-center rounded-lg text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--card-hover)] border border-[var(--border)] transition-colors shadow-xs">
                <i data-lucide="bell" class="w-4 h-4"></i>
                @if($unreadNotifs->count() > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
                @endif
            </button>

            {{-- Dropdown --}}
            <div class="notif-dropdown">
                <div class="flex items-center justify-between p-3.5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50">
                    <h3 class="text-xs font-bold text-[var(--text-primary)] uppercase tracking-wider">Notifikasi</h3>
                    @if($unreadNotifs->count() > 0)
                        <form action="{{ route('notifikasi.read-all') }}" method="POST">
                            @csrf
                            <button class="text-xs text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 font-semibold">Tandai semua dibaca</button>
                        </form>
                    @endif
                </div>
                <div class="max-h-72 overflow-y-auto divide-y divide-[var(--border)]">
                    @forelse($recentNotifs as $notif)
                        <a href="{{ route('notifikasi.read', $notif->id) }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-[var(--card-hover)] transition-colors {{ !$notif->isRead() ? 'bg-indigo-50/40 dark:bg-indigo-950/20' : '' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5
                                {{ $notif->type==='success' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400' :
                                   ($notif->type==='warning' ? 'bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400'  :
                                   ($notif->type==='danger'  ? 'bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-400'    :
                                                               'bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400')) }}">
                                <i data-lucide="{{ $notif->icon ?? 'bell' }}" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[var(--text-primary)] truncate">{{ $notif->title }}</p>
                                @if($notif->body)
                                    <p class="text-[11px] text-[var(--text-secondary)] line-clamp-2 mt-0.5">{{ $notif->body }}</p>
                                @endif
                                <p class="text-[10px] text-[var(--text-muted)] mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notif->isRead())
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 shrink-0 mt-2"></span>
                            @endif
                        </a>
                    @empty
                        <div class="py-8 text-center">
                            <i data-lucide="bell-off" class="w-7 h-7 text-[var(--text-muted)] mx-auto mb-2 opacity-50"></i>
                            <p class="text-xs text-[var(--text-secondary)]">Tidak ada notifikasi baru</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- User profile avatar + dropdown --}}
        <div class="relative group">
            <button class="flex items-center gap-2 p-1 rounded-lg hover:bg-[var(--card-hover)] border border-[var(--border)] transition-colors shadow-xs">
                <img src="{{ Auth::user()->avatar_url }}"
                     class="w-7 h-7 rounded-md object-cover ring-1 ring-slate-300 dark:ring-slate-700" alt="{{ Auth::user()->nama }}">
                <span class="text-xs font-semibold text-[var(--text-primary)] hidden md:block max-w-[120px] truncate">{{ Auth::user()->nama }}</span>
                <i data-lucide="chevron-down" class="w-3 h-3 text-[var(--text-muted)] hidden sm:block"></i>
            </button>

            <div class="user-dropdown">
                <div class="p-3.5 border-b border-[var(--border)] bg-slate-50/50 dark:bg-slate-900/50">
                    <p class="text-xs font-bold text-[var(--text-primary)] truncate">{{ Auth::user()->nama }}</p>
                    <p class="text-[11px] text-[var(--text-secondary)] mt-0.5 truncate">{{ Auth::user()->nip ?? Auth::user()->email }}</p>
                    <span class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 uppercase tracking-wider">
                        {{ Auth::user()->role_label ?? ucfirst(Auth::user()->role) }}
                    </span>
                </div>
                <div class="p-1.5 space-y-0.5">
                    <a href="{{ route('profil.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--card-hover)] font-medium transition-colors">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i> Profil Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-medium transition-colors">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>

