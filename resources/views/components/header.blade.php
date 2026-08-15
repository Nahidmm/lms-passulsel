<header class="bg-white border-b border-border h-16 flex items-center justify-between px-4 lg:px-8 shrink-0 sticky top-0 z-30">
    
    <!-- Mobile menu button -->
    <div class="md:hidden flex items-center gap-3">
        <button class="text-text-secondary hover:text-primary p-1">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <div class="font-display font-bold text-primary text-lg">LMS Pas Sulsel</div>
    </div>

    <!-- Desktop Title (Breadcrumb placeholder) -->
    <div class="hidden md:block">
        <h2 class="font-display font-semibold text-lg text-text-primary">@yield('title', 'Dashboard')</h2>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center gap-4 lg:gap-6">
        
        <!-- Today's Date -->
        <div class="hidden sm:flex items-center gap-2 text-sm text-text-secondary font-medium">
            <i data-lucide="calendar" class="w-4 h-4"></i>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>

        <div class="h-6 w-px bg-border hidden sm:block"></div>

        <!-- User Dropdown (Simple logout for now) -->
        <div class="relative group">
            <button class="flex items-center gap-2 hover:bg-secondary px-2 py-1 rounded-lg transition-colors">
                <span class="text-sm font-medium text-text-primary hidden sm:block">{{ explode(' ', Auth::user()->nama)[0] }}</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-text-secondary"></i>
            </button>
            
            <!-- Dropdown Menu -->
            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 transform origin-top-right">
                <div class="p-2 border-b border-border">
                    <p class="text-sm font-semibold text-text-primary truncate">{{ Auth::user()->nama }}</p>
                    <p class="text-xs text-text-secondary truncate">{{ Auth::user()->nip }}</p>
                </div>
                <div class="p-1">
                    <a href="{{ route('profil.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-text-primary hover:bg-secondary rounded-md">
                        <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-danger hover:bg-danger/10 rounded-md">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>
