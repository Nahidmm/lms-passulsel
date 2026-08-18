<header class="bg-white border-b border-border h-16 flex items-center justify-between px-4 lg:px-8 shrink-0 sticky top-0 z-30">
    
    <!-- Mobile menu button -->
    <div class="md:hidden flex items-center gap-3">
        <button class="text-text-secondary hover:text-primary p-1">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <img src="{{ asset('logo/logo.png') }}" alt="Logo LMS" class="h-6 w-auto object-contain">
        <div class="font-display font-bold text-primary text-lg">LMS Pas Sulsel</div>
    </div>

    <!-- Left Side: Search Bar (Desktop) -->
    <div class="hidden md:flex flex-1 max-w-md">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="w-4 h-4 text-text-secondary"></i>
            </div>
            <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 bg-secondary/50 border border-border rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all text-text-primary placeholder-text-secondary">
        </div>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center gap-4 lg:gap-6 ml-auto">
        
        <!-- Notification Bell -->
        @php
            $unreadNotifs = \App\Models\Notification::where('user_id', Auth::id())
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->get();
            $recentNotifs = \App\Models\Notification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        @endphp
        
        <div class="relative group" id="notification-dropdown">
            <button class="relative p-2 text-text-secondary hover:text-primary hover:bg-primary/5 rounded-full transition-colors focus:outline-none">
                <i data-lucide="bell" class="w-5 h-5"></i>
                @if($unreadNotifs->count() > 0)
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-danger rounded-full border-2 border-white animate-pulse"></span>
                @endif
            </button>
            
            <!-- Notification Dropdown Menu -->
            <div class="absolute top-full mt-2 right-0 w-80 bg-white rounded-xl shadow-lg border border-border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 transform origin-top-right">
                <div class="p-3 border-b border-border flex items-center justify-between">
                    <h3 class="font-bold text-text-primary text-sm">Notifikasi</h3>
                    @if($unreadNotifs->count() > 0)
                        <form action="{{ route('notifikasi.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-primary hover:underline font-medium">Tandai Semua Dibaca</button>
                        </form>
                    @endif
                </div>
                
                <div class="max-h-[300px] overflow-y-auto">
                    @forelse($recentNotifs as $notif)
                        <a href="{{ route('notifikasi.read', $notif->id) }}" class="block p-3 border-b border-border/50 hover:bg-secondary/50 transition-colors {{ !$notif->isRead() ? 'bg-primary/5' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 shrink-0 w-8 h-8 rounded-full flex items-center justify-center 
                                    {{ $notif->type === 'success' ? 'bg-success/10 text-success' : 
                                       ($notif->type === 'warning' ? 'bg-warning/10 text-warning' : 
                                       ($notif->type === 'danger' ? 'bg-danger/10 text-danger' : 'bg-primary/10 text-primary')) }}">
                                    <i data-lucide="{{ $notif->icon }}" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-text-primary mb-0.5 {{ !$notif->isRead() ? 'text-primary' : '' }}">{{ $notif->title }}</p>
                                    @if($notif->body)
                                        <p class="text-xs text-text-secondary line-clamp-2 mb-1">{{ $notif->body }}</p>
                                    @endif
                                    <p class="text-[10px] text-text-secondary">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                @if(!$notif->isRead())
                                    <span class="w-2 h-2 rounded-full bg-primary shrink-0 mt-1"></span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center">
                            <i data-lucide="bell-off" class="w-8 h-8 text-text-secondary mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm text-text-secondary">Belum ada notifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="relative group flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-xs text-text-secondary">Hello</p>
                <p class="text-sm font-bold text-text-primary leading-tight">{{ explode(' ', Auth::user()->nama)[0] }}</p>
            </div>
            <button class="flex items-center gap-2 hover:bg-secondary rounded-full transition-colors focus:outline-none">
                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-9 h-9 rounded-full border-2 border-white shadow-sm object-cover">
            </button>
            
            <!-- Dropdown Menu -->
            <div class="absolute top-full mt-2 right-0 w-48 bg-white rounded-xl shadow-lg border border-border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 transform origin-top-right">
                <div class="p-3 border-b border-border">
                    <p class="text-sm font-semibold text-text-primary truncate">{{ Auth::user()->nama }}</p>
                    <p class="text-xs text-text-secondary truncate">{{ Auth::user()->nip }}</p>
                </div>
                <div class="p-1.5">
                    <a href="{{ route('profil.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-text-secondary hover:text-primary hover:bg-primary/5 rounded-lg transition-colors">
                        <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-danger hover:bg-danger/10 rounded-lg transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>
