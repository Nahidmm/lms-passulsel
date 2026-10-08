<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="STRAPSUSPAS - Petugas Paten, Pembinaan Pasti, Pemasyarakatan Berdampak | Kanwil Ditjenpas Sulsel.">
    <title>@yield('title', 'Dashboard') - STRAPSUSPAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo/strapsuspas.png') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="antialiased min-h-screen text-[var(--text-primary)] bg-[var(--bg)]" id="app-body">

    {{-- ── MOBILE OVERLAY (backdrop when drawer open) ── --}}
    <div id="mobile-overlay"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"
         onclick="closeSidebar()">
    </div>

    {{-- ── LAYOUT WRAPPER ── --}}
    <div class="app-layout">

        {{-- Sidebar (desktop: always visible | mobile: drawer) --}}
        @include('components.sidebar')

        {{-- Main area --}}
        <div class="app-main" id="app-main">

            {{-- Sticky top header --}}
            @include('components.header')

            {{-- Page content --}}
            <main class="app-content @yield('content-class')" id="app-content">

                @if(session('success'))
                    <div class="alert alert-success mb-5">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error mb-5">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning mb-5">
                        <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info mb-5">
                        <i data-lucide="info" class="w-5 h-5 shrink-0"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')

                {{-- ── FOOTER ── --}}
                <footer class="mt-12 pt-6 border-t border-[var(--border)] text-center text-xs text-[var(--text-muted)] flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p>&copy; {{ date('Y') }} STRAPSUSPAS &bull; Kanwil Ditjenpas Sulawesi Selatan</p>
                    <p class="flex items-center justify-center gap-1 font-medium text-[var(--text-secondary)]">
                        Crafted by <span class="font-bold text-[var(--text-primary)]">IR & ANM</span>
                    </p>
                </footer>

                {{-- Bottom padding so content is not hidden behind mobile bottom nav --}}
                <div class="h-20 md:hidden"></div>
            </main>
        </div>
    </div>

    {{-- ── MOBILE BOTTOM NAV BAR ── --}}
    <nav class="mobile-bottom-nav md:hidden" id="mobile-bottom-nav">
        <a href="{{ route('dashboard') }}"
           class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Dashboard</span>
        </a>

        @if(Auth::user()->isPeserta())
            <a href="{{ route('peserta.pelatihan.index') }}"
               class="mobile-nav-item {{ request()->routeIs('peserta.pelatihan.*', 'peserta.pembelajaran.*') ? 'active' : '' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span>Pelatihan</span>
            </a>
            <a href="{{ route('peserta.statistik.index') }}"
               class="mobile-nav-item {{ request()->routeIs('peserta.statistik.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                <span>Statistik</span>
            </a>
            @if(!Auth::user()->hasActiveSesiEvaluasi())
            <a href="{{ route('ai.index') }}"
               class="mobile-nav-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                <i data-lucide="bot" class="w-5 h-5"></i>
                <span>AI Tutor</span>
            </a>
            @endif
        @else
            <a href="{{ route('admin.pelatihan.index') }}"
               class="mobile-nav-item {{ request()->routeIs('admin.pelatihan.*') ? 'active' : '' }}">
                <i data-lucide="folder-open" class="w-5 h-5"></i>
                <span>Pelatihan</span>
            </a>
            <a href="{{ route('admin.akun.index') }}"
               class="mobile-nav-item {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Akun</span>
                @php $pc = \App\Models\AccountRequest::where('status','menunggu')->count(); @endphp
                @if($pc > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full text-[8px] font-black text-[var(--text-primary)] flex items-center justify-center">{{ $pc }}</span>
                @endif
            </a>
            <a href="{{ route('admin.gradebook.index') }}"
               class="mobile-nav-item {{ request()->routeIs('admin.gradebook.*', 'admin.pelatihan.gradebook', 'admin.penilaian.*') ? 'active' : '' }}">
                <i data-lucide="award" class="w-5 h-5"></i>
                <span>Buku Nilai</span>
            </a>
        @endif

        {{-- More / hamburger --}}
        <button class="mobile-nav-item" onclick="toggleSidebar()">
            <i data-lucide="menu" class="w-5 h-5"></i>
            <span>Menu</span>
        </button>
    </nav>

    @stack('scripts')
    <script>
        lucide.createIcons();

        // ── Theme Toggle Logic ──
        const themeToggleBtn = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');

        function updateThemeIcons() {
            if(!themeToggleBtn) return;
            if (document.documentElement.classList.contains('dark')) {
                lightIcon?.classList.remove('hidden');
                darkIcon?.classList.add('hidden');
                document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#0f172a');
            } else {
                darkIcon?.classList.remove('hidden');
                lightIcon?.classList.add('hidden');
                document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#ffffff');
            }
        }
        
        if (themeToggleBtn) {
            updateThemeIcons();
            themeToggleBtn.addEventListener('click', function () {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                updateThemeIcons();
            });
        }

        // ── Sidebar: mobile drawer ──
        function openSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const body    = document.getElementById('app-body');
            sidebar?.classList.add('sidebar-open');
            overlay?.classList.remove('opacity-0', 'pointer-events-none');
            overlay?.classList.add('opacity-100');
            if (body) body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const body    = document.getElementById('app-body');
            sidebar?.classList.remove('sidebar-open');
            overlay?.classList.add('opacity-0', 'pointer-events-none');
            overlay?.classList.remove('opacity-100');
            if (body) body.style.overflow = '';
        }

        // ── Sidebar toggle: collapse on desktop, drawer on mobile ──
        function toggleSidebar() {
            if (window.innerWidth >= 768) {
                const sidebar = document.getElementById('app-sidebar');
                const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sb-collapsed', isCollapsed ? '1' : '0');
            } else {
                const sidebar = document.getElementById('app-sidebar');
                if (sidebar?.classList.contains('sidebar-open')) closeSidebar();
                else openSidebar();
            }
        }

        // ── Restore sidebar state on load (Desktop defaults to expanded) ──
        (function() {
            const sidebar = document.getElementById('app-sidebar');
            if (!sidebar) return;
            if (window.innerWidth >= 768) {
                const saved = localStorage.getItem('sb-collapsed');
                if (saved === '1') {
                    sidebar.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                }
            }
        })();

        // Close mobile drawer on nav link click
        document.querySelectorAll('#app-sidebar .nav-item').forEach(el => {
            el.addEventListener('click', () => {
                if (window.innerWidth < 768) closeSidebar();
            });
        });

        // Close drawer on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) closeSidebar();
        });
    </script>
</body>
</html>

