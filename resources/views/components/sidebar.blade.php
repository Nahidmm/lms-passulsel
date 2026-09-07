{{-- ── MODERN SAAS SIDEBAR ── --}}
<aside id="app-sidebar" class="sidebar-game">

    {{-- Brand Logo Header --}}
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0" title="STRAPSUSPAS - Kanwil Ditjenpas Sulsel">
            <img src="{{ asset('logo/strapsuspas.png') }}" alt="Logo STRAPSUSPAS" class="w-9 h-9 object-contain shrink-0">
            <div class="leading-tight min-w-0">
                <p class="text-sm font-bold tracking-tight text-[var(--text-primary)] truncate">STRAPSUSPAS</p>
                <p class="text-[10px] font-medium text-[var(--text-muted)] truncate">Kanwil Ditjenpas Sulsel</p>
            </div>
        </a>
        {{-- Close button (mobile only) --}}
        <button onclick="closeSidebar()"
            class="ml-auto w-8 h-8 rounded-lg flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-[var(--border)] transition-colors md:hidden"
            aria-label="Tutup Menu">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    {{-- Navigation List --}}
    <nav class="flex-1 overflow-y-auto px-2 py-2.5 space-y-0.5">

        <p class="nav-section-label">Menu Utama</p>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           title="{{ Auth::user()->isPeserta() ? 'Dashboard Peserta' : 'Dashboard Admin' }}">
            <i data-lucide="layout-dashboard"></i>
            <span>{{ Auth::user()->isPeserta() ? 'Dashboard Peserta' : 'Dashboard Admin' }}</span>
        </a>

        @if(Auth::user()->isPeserta())

            <div class="nav-divider"></div>
            <p class="nav-section-label">Pembelajaran</p>

            <a href="{{ route('peserta.pelatihan.index') }}"
                class="nav-item {{ request()->routeIs('peserta.pelatihan.*', 'peserta.pembelajaran.*') ? 'active' : '' }}"
                title="Katalog Pelatihan">
                <i data-lucide="book-open"></i>
                <span>Katalog Pelatihan</span>
            </a>

            <a href="{{ route('peserta.statistik.index') }}"
                class="nav-item {{ request()->routeIs('peserta.statistik.*') ? 'active' : '' }}"
                title="Statistik & Nilai">
                <i data-lucide="bar-chart-2"></i>
                <span>Statistik & Nilai</span>
            </a>

            @if(!Auth::user()->hasActiveSesiEvaluasi())
                <a href="{{ route('ai.index') }}"
                   class="nav-item {{ request()->routeIs('ai.*') ? 'active' : '' }}"
                   title="AI Tutor Disiplin ASN">
                    <i data-lucide="sparkles" class="text-indigo-500"></i>
                    <span>AI Tutor Disiplin</span>
                    <span class="ml-auto text-[10px] font-bold bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300 px-1.5 py-0.5 rounded-full border border-indigo-200 dark:border-indigo-800">AI</span>
                </a>
            @endif

        @else

            <div class="nav-divider"></div>
            <p class="nav-section-label">Manajemen Diklat</p>

            <a href="{{ route('admin.pelatihan.index') }}"
                class="nav-item {{ request()->routeIs('admin.pelatihan.*', 'admin.materi.*', 'admin.tugas.*') ? 'active' : '' }}"
                title="Kelola Pelatihan">
                <i data-lucide="folder-open"></i>
                <span>Kelola Pelatihan</span>
            </a>

            <a href="{{ route('admin.pretest.index') }}"
                class="nav-item {{ request()->routeIs('admin.pretest.*') ? 'active' : '' }}"
                title="Kelola Pretest Diagnostik">
                <i data-lucide="clipboard-check"></i>
                <span>Kelola Pretest</span>
            </a>

            <a href="{{ route('admin.gradebook.index') }}"
                class="nav-item {{ request()->routeIs('admin.gradebook.*', 'admin.pelatihan.gradebook', 'admin.penilaian.*', 'admin.statistik.*') ? 'active' : '' }}"
                title="Buku Nilai (Gradebook)">
                <i data-lucide="award"></i>
                <span>Buku Nilai (Gradebook)</span>
            </a>

            <div class="nav-divider"></div>
            <p class="nav-section-label">Kepegawaian</p>

            <a href="{{ route('admin.akun.index') }}"
                class="nav-item {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}"
                title="Manajemen Akun">
                <i data-lucide="users"></i>
                <span>Manajemen Akun</span>
                @php $pendingCount = \App\Models\AccountRequest::where('status', 'menunggu')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto text-[10px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300 px-2 py-0.5 rounded-full border border-rose-200 dark:border-rose-800">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.jabatan.index') }}"
                class="nav-item {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}"
                title="Kelola Data Jabatan">
                <i data-lucide="briefcase"></i>
                <span>Kelola Jabatan</span>
            </a>

            <a href="{{ route('admin.unit-kerja.index') }}"
                class="nav-item {{ request()->routeIs('admin.unit-kerja.*') ? 'active' : '' }}"
                title="Kelola Unit Kerja (UPT)">
                <i data-lucide="building-2"></i>
                <span>Kelola Unit Kerja</span>
            </a>

            <a href="{{ route('admin.kalender.index') }}"
                class="nav-item {{ request()->routeIs('admin.kalender.*') ? 'active' : '' }}"
                title="Kalender Agenda Diklat">
                <i data-lucide="calendar"></i>
                <span>Kalender Diklat</span>
            </a>

            @if(Auth::user()->isSuperadmin())
                <div class="nav-divider"></div>
                <p class="nav-section-label">Konfigurasi Sistem</p>

                <a href="{{ route('admin.sertifikat-setting.edit') }}"
                    class="nav-item {{ request()->routeIs('admin.sertifikat-setting.*') ? 'active' : '' }}"
                    title="Template Sertifikat">
                    <i data-lucide="file-badge"></i>
                    <span>Template Sertifikat</span>
                </a>

                <a href="{{ route('admin.kelola-akses.index') }}"
                    class="nav-item {{ request()->routeIs('admin.kelola-akses.index', 'admin.kelola-akses.roles.*') ? 'active' : '' }}"
                    title="Hak Akses & Role">
                    <i data-lucide="shield-check"></i>
                    <span>Hak Akses & Role</span>
                </a>
                <a href="{{ route('admin.kelola-akses.users') }}"
                    class="nav-item {{ request()->routeIs('admin.kelola-akses.users') ? 'active' : '' }}"
                    title="Tetapkan Role Pengguna">
                    <i data-lucide="user-check"></i>
                    <span>Tetapkan Role</span>
                </a>
                <a href="{{ route('admin.kelola-akses.dokumen-ai.index') }}"
                    class="nav-item {{ request()->routeIs('admin.kelola-akses.dokumen-ai.*') ? 'active' : '' }}"
                    title="Basis Pengetahuan AI (RAG)">
                    <i data-lucide="database"></i>
                    <span>Basis Pengetahuan AI</span>
                </a>
            @endif

        @endif
    </nav>

    {{-- User Profile Mini / Logout --}}
    <div class="p-2.5 shrink-0 border-t border-[var(--border)]">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                title="Keluar dari Akun"
                class="sidebar-logout-btn w-full flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-xs text-[var(--text-secondary)] hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
                <span class="sidebar-logout-text">Keluar dari Akun</span>
            </button>
        </form>
    </div>
</aside>