<aside class="bg-white text-text-primary w-64 shrink-0 h-screen sticky top-0 flex flex-col hidden md:flex transition-all duration-300 z-40 border-r border-border">
    <!-- Logo & Branding -->
    <div class="h-16 flex items-center px-6 border-b border-border shrink-0">
        <img src="{{ asset('logo/logo.png') }}" alt="Logo LMS" class="h-8 w-auto object-contain mr-3">
        <div class="font-display font-bold text-lg tracking-wide truncate text-text-primary">LMS Pas Sulsel</div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-1.5">
        
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="text-sm">Dashboard</span>
        </a>

        @if(Auth::user()->isPeserta())
            <div class="text-xs font-semibold text-text-secondary/60 uppercase tracking-wider mt-5 mb-2 px-4">Pembelajaran</div>
            
            <a href="{{ route('peserta.pelatihan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('peserta.pelatihan.*', 'peserta.pembelajaran.*') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span class="text-sm">Katalog Pelatihan</span>
            </a>
            
            <a href="{{ route('peserta.evaluasi.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('peserta.evaluasi.*') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span class="text-sm">Evaluasi / Kuis</span>
            </a>
            
            <a href="{{ route('peserta.statistik.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('peserta.statistik.*') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                <span class="text-sm">Statistik Belajar</span>
            </a>

            @if(!Auth::user()->hasActiveSesiEvaluasi())
            <a href="{{ route('ai.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors mt-2 {{ request()->routeIs('ai.*') ? 'bg-accent/10 text-accent-hover font-bold shadow-sm border border-accent/20' : 'text-text-secondary hover:bg-accent/5 hover:text-accent-hover' }}">
                <i data-lucide="bot" class="w-5 h-5"></i>
                <span class="text-sm">AI Assistant</span>
            </a>
            @endif

        @else
            <!-- Menu Admin / Superadmin -->
            <div class="text-xs font-semibold text-text-secondary/60 uppercase tracking-wider mt-5 mb-2 px-4">Manajemen Konten</div>
            
            <a href="{{ route('admin.pelatihan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.pelatihan.*', 'admin.materi.*') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span class="text-sm">Kelola Pelatihan</span>
            </a>
            
            <div class="text-xs font-semibold text-text-secondary/60 uppercase tracking-wider mt-5 mb-2 px-4">Laporan & Pengguna</div>
            
            <a href="{{ route('admin.statistik.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.statistik.*') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
                <span class="text-sm">Statistik Peserta</span>
            </a>
            
            <a href="{{ route('admin.akun.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.akun.*') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="text-sm">Manajemen Akun</span>
                @php
                    $pendingCount = \App\Models\AccountRequest::where('status', 'menunggu')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-danger/10 text-danger text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
            
            <a href="{{ route('admin.kalender.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.kalender.*') ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-primary/5 hover:text-primary' }}">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span class="text-sm">Kalender Akademik</span>
            </a>
        @endif
        
        <div class="mt-auto pt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors text-text-secondary hover:bg-danger/10 hover:text-danger">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="text-sm font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
