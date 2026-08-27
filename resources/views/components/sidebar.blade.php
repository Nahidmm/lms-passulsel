{{-- â•â•â• SIDEBAR â€” Cinematic dark glass panel â•â•â• --}}
<aside id="app-sidebar" class="sidebar-game">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i data-lucide="zap" class="w-4 h-4 text-[var(--text-primary)]"></i>
        </div>
        <div class="leading-none">
            <p class="text-sm font-black text-[var(--text-primary)] tracking-tight" style="font-family:'Fraunces',serif;">SPEKTRA</p>
            <p class="text-[9px] font-bold uppercase tracking-[0.18em]" style="color:var(--amber-bright);">Pas Sulsel</p>
        </div>
        {{-- Close button (mobile only) --}}
        <button onclick="closeSidebar()"
            class="ml-auto w-8 h-8 rounded-xl flex items-center justify-center transition-all md:hidden"
            style="color:var(--text-muted);" onmouseenter="this.style.color='var(--paper)'" onmouseleave="this.style.color='var(--text-muted)'">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>


    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-2">

        <p class="nav-section-label">Utama</p>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i>
            <span>{{ Auth::user()->isPeserta() ? 'Dashboard Peserta' : 'Dashboard Admin' }}</span>
        </a>

        @if(Auth::user()->isPeserta())

            <div class="nav-divider"></div>
            <p class="nav-section-label">Belajar</p>

            <a href="{{ route('peserta.pelatihan.index') }}" class="nav-item {{ request()->routeIs('peserta.pelatihan.*', 'peserta.pembelajaran.*') ? 'active' : '' }}">
                <i data-lucide="book-open"></i>
                <span>Katalog Pelatihan</span>
            </a>

            <a href="{{ route('peserta.statistik.index') }}" class="nav-item {{ request()->routeIs('peserta.statistik.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2"></i>
                <span>Statistik Saya</span>
            </a>

            @if(!Auth::user()->hasActiveSesiEvaluasi())
                <a href="{{ route('ai.index') }}" class="nav-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                    <i data-lucide="bot" class="text-cyan-400"></i>
                    <span>AI Tutor</span>
                    <span class="ml-auto text-[9px] font-black bg-cyan-500/15 text-cyan-400 border border-cyan-500/25 px-1.5 py-0.5 rounded-full">AI</span>
                </a>
            @endif

        @else

            <div class="nav-divider"></div>
            <p class="nav-section-label">Kelola</p>

            <a href="{{ route('admin.pelatihan.index') }}" class="nav-item {{ request()->routeIs('admin.pelatihan.*', 'admin.materi.*') ? 'active' : '' }}">
                <i data-lucide="folder-open"></i>
                <span>Kelola Pelatihan</span>
            </a>

            <a href="{{ route('admin.pretest.index') }}" class="nav-item {{ request()->routeIs('admin.pretest.*') ? 'active' : '' }}">
                <i data-lucide="clipboard-check"></i>
                <span>Kelola Pretest</span>
            </a>

            <a href="{{ route('admin.statistik.index') }}" class="nav-item {{ request()->routeIs('admin.statistik.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2"></i>
                <span>Statistik Peserta</span>
            </a>

            <a href="{{ route('admin.penilaian.index') }}" class="nav-item {{ request()->routeIs('admin.penilaian.*') ? 'active' : '' }}">
                <i data-lucide="clipboard-list"></i>
                <span>Penilaian</span>
            </a>

            <a href="{{ route('admin.akun.index') }}" class="nav-item {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Manajemen Akun</span>
                @php $pendingCount = \App\Models\AccountRequest::where('status', 'menunggu')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto text-[9px] font-black bg-rose-500/15 text-rose-400 border border-rose-500/25 px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.jabatan.index') }}" class="nav-item {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}">
                <i data-lucide="briefcase"></i>
                <span>Kelola Jabatan</span>
            </a>

            <a href="{{ route('admin.unit-kerja.index') }}" class="nav-item {{ request()->routeIs('admin.unit-kerja.*') ? 'active' : '' }}">
                <i data-lucide="building-2"></i>
                <span>Kelola Unit Kerja</span>
            </a>

            <a href="{{ route('admin.kalender.index') }}" class="nav-item {{ request()->routeIs('admin.kalender.*') ? 'active' : '' }}">
                <i data-lucide="calendar"></i>
                <span>Kalender Akademik</span>
            </a>

            @if(Auth::user()->isSuperadmin())
                <div class="nav-divider"></div>
                <p class="nav-section-label">Sistem</p>

                <a href="{{ route('admin.sertifikat-setting.edit') }}" class="nav-item {{ request()->routeIs('admin.sertifikat-setting.*') ? 'active' : '' }}">
                    <i data-lucide="award"></i>
                    <span>Sertifikat</span>
                </a>

                <a href="{{ route('admin.kelola-akses.index') }}" class="nav-item {{ request()->routeIs('admin.kelola-akses.index','admin.kelola-akses.roles.*') ? 'active' : '' }}">
                    <i data-lucide="shield-check"></i>
                    <span>Kelola Akses</span>
                </a>
                <a href="{{ route('admin.kelola-akses.users') }}" class="nav-item {{ request()->routeIs('admin.kelola-akses.users') ? 'active' : '' }}">
                    <i data-lucide="user-cog"></i>
                    <span>Assign Role</span>
                </a>
                <a href="{{ route('admin.kelola-akses.dokumen-ai.index') }}" class="nav-item {{ request()->routeIs('admin.kelola-akses.dokumen-ai.*') ? 'active' : '' }}">
                    <i data-lucide="database"></i>
                    <span>Knowledge Base AI</span>
                </a>
            @endif

        @endif
    </nav>

    {{-- Logout --}}
    <div class="p-3 shrink-0" style="border-top:1px solid rgba(255,255,255,0.05);">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-logout-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm transition-all"
                style="color:var(--text-muted);" onmouseenter="this.style.color='#fda4af';this.style.background='rgba(244,63,94,0.08)'" onmouseleave="this.style.color='var(--text-muted)';this.style.background='transparent'">
                <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
                <span class="sidebar-logout-text">Keluar</span>
            </button>
        </form>
    </div>
</aside>




