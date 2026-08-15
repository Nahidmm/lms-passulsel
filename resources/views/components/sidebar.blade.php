<aside class="bg-primary text-white w-64 shrink-0 h-screen sticky top-0 flex flex-col hidden md:flex transition-all duration-300 z-40">
    <!-- Logo & Branding -->
    <div class="h-16 flex items-center px-6 border-b border-white/10 shrink-0">
        <div class="w-8 h-8 bg-white/10 rounded-full flex items-center justify-center text-accent font-display font-bold text-xs shadow-sm mr-3">
            PAS
        </div>
        <div class="font-display font-bold text-lg tracking-wide truncate">LMS Pas Sulsel</div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 flex flex-col gap-1">
        
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Dashboard</span>
        </a>

        @if(Auth::user()->isPeserta())
            <div class="text-xs font-semibold text-white/40 uppercase tracking-wider mt-4 mb-2 px-3">Pembelajaran</div>
            
            <a href="{{ route('peserta.pembelajaran.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('peserta.pembelajaran.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span>Modul Pembelajaran</span>
            </a>
            
            <a href="{{ route('peserta.evaluasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('peserta.evaluasi.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span>Evaluasi / Kuis</span>
            </a>
            
            <a href="{{ route('peserta.statistik.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('peserta.statistik.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                <span>Statistik Belajar</span>
            </a>

            @if(!Auth::user()->hasActiveSesiEvaluasi())
            <a href="{{ route('ai.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors mt-2 {{ request()->routeIs('ai.*') ? 'bg-accent text-primary font-bold shadow-md' : 'border border-white/20 text-white hover:bg-white/10' }}">
                <i data-lucide="bot" class="w-5 h-5 {{ request()->routeIs('ai.*') ? 'text-primary' : 'text-accent' }}"></i>
                <span>AI Assistant</span>
            </a>
            @endif

        @else
            <!-- Menu Admin / Superadmin -->
            <div class="text-xs font-semibold text-white/40 uppercase tracking-wider mt-4 mb-2 px-3">Manajemen Konten</div>
            
            <a href="{{ route('admin.materi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.materi.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="library" class="w-5 h-5"></i>
                <span>Kelola Modul</span>
            </a>
            
            <a href="{{ route('admin.video.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.video.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="video" class="w-5 h-5"></i>
                <span>Kelola Video</span>
            </a>
            
            <a href="{{ route('admin.soal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.soal.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="help-circle" class="w-5 h-5"></i>
                <span>Kelola Soal</span>
            </a>

            <div class="text-xs font-semibold text-white/40 uppercase tracking-wider mt-4 mb-2 px-3">Laporan & Pengguna</div>
            
            <a href="{{ route('admin.statistik.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.statistik.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
                <span>Statistik Peserta</span>
            </a>
            
            <a href="{{ route('admin.akun.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.akun.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Manajemen Akun</span>
                @php
                    $pendingCount = \App\Models\AccountRequest::where('status', 'menunggu')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-accent text-primary text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
            
            <a href="{{ route('admin.kalender.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.kalender.*') ? 'bg-white/10 text-accent font-semibold relative after:absolute after:left-0 after:top-1/2 after:-translate-y-1/2 after:h-6 after:w-1 after:bg-accent after:rounded-r-md' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span>Kalender Akademik</span>
            </a>
        @endif
    </nav>

    <!-- Bottom Settings Profile -->
    <div class="border-t border-white/10 p-4 shrink-0">
        <a href="{{ route('profil.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('profil.*') ? 'bg-white/10 text-accent font-semibold' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-8 h-8 rounded-full border border-white/20">
            <div class="flex flex-col min-w-0">
                <span class="text-sm font-medium truncate">{{ Auth::user()->nama }}</span>
                <span class="text-xs text-white/50 capitalize">{{ Auth::user()->role }}</span>
            </div>
        </a>
    </div>
</aside>
