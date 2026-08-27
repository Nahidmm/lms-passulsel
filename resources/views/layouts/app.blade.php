<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0b0e13">
    <meta name="description" content="SPEKTRA Ã¢â‚¬â€ Platform pembelajaran dan evaluasi berbasis cinematic experience.">
    <title>@yield('title', 'Dashboard') - SPEKTRA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%23c8891a'/><text y='24' x='4' font-size='22' font-family='serif'>Ã¢Å¡Â¡</text></svg>">
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
<body class="game-shell antialiased overflow-x-hidden" id="app-body" style="--mx:0;--my:0;">

    {{-- ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â MOBILE OVERLAY (backdrop when drawer open) ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â --}}
    <div id="mobile-overlay"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"
         onclick="closeSidebar()">
    </div>

    {{-- ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â LAYOUT WRAPPER ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â --}}
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

                {{-- Bottom padding so content is not hidden behind mobile bottom nav --}}
                <div class="h-20 md:hidden"></div>
            </main>
        </div>
    </div>

    {{-- ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â MOBILE BOTTOM NAV BAR ÃƒÂ¢Ã¢â‚¬Â¢Ã‚ÂÃƒÂ¢Ã¢â‚¬Â¢Ã‚Â --}}
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
            <a href="{{ route('admin.statistik.index') }}"
               class="mobile-nav-item {{ request()->routeIs('admin.statistik.*') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                <span>Statistik</span>
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
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
                document.querySelector('meta[name="theme-color"]').setAttribute('content', '#0b0e13');
            } else {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
                document.querySelector('meta[name="theme-color"]').setAttribute('content', '#fdf4e3');
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

        // Ã¢â€â‚¬Ã¢â€â‚¬ Sidebar: mobile drawer Ã¢â€â‚¬Ã¢â€â‚¬
        function openSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const body    = document.getElementById('app-body');
            sidebar.classList.add('sidebar-open');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const body    = document.getElementById('app-body');
            sidebar.classList.remove('sidebar-open');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100');
            body.style.overflow = '';
        }

        // Ã¢â€â‚¬Ã¢â€â‚¬ Sidebar toggle: collapse on desktop, drawer on mobile Ã¢â€â‚¬Ã¢â€â‚¬
        function toggleSidebar() {
            if (window.innerWidth >= 768) {
                // Desktop: toggle icon-only collapse
                const sidebar = document.getElementById('app-sidebar');
                const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sb-collapsed', isCollapsed ? '1' : '0');
            } else {
                // Mobile: open/close drawer
                const sidebar = document.getElementById('app-sidebar');
                if (sidebar.classList.contains('sidebar-open')) closeSidebar();
                else openSidebar();
            }
        }

        // Ã¢â€â‚¬Ã¢â€â‚¬ Restore sidebar state on load Ã¢â€â‚¬Ã¢â€â‚¬
        (function() {
            const sidebar = document.getElementById('app-sidebar');
            if (!sidebar) return;
            if (window.innerWidth >= 768) {
                // Default: collapsed unless user explicitly expanded
                const saved = localStorage.getItem('sb-collapsed');
                // null = first visit Ã¢â€ â€™ default collapsed
                if (saved === null || saved === '1') {
                    sidebar.classList.add('sidebar-collapsed');
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

        // Ã¢â€â‚¬Ã¢â€â‚¬ Global pointer parallax for CSS vars --mx/--my Ã¢â€â‚¬Ã¢â€â‚¬
        (function() {
            const root = document.documentElement;
            let txMX = 0, txMY = 0, mX = 0, mY = 0, raf = false;
            const rm = window.matchMedia('(prefers-reduced-motion: reduce)');
            function lerp(a, b, t) { return a + (b - a) * t; }
            function tick() {
                raf = false;
                mX = lerp(mX, txMX, 0.08);
                mY = lerp(mY, txMY, 0.08);
                if (!rm.matches) {
                    root.style.setProperty('--mx', mX.toFixed(4));
                    root.style.setProperty('--my', mY.toFixed(4));
                }
                if (Math.abs(mX - txMX) > 0.001 || Math.abs(mY - txMY) > 0.001) {
                    raf = true; requestAnimationFrame(tick);
                }
            }
            window.addEventListener('pointermove', function(e) {
                txMX = e.clientX / window.innerWidth - 0.5;
                txMY = e.clientY / window.innerHeight - 0.5;
                if (!raf) { raf = true; requestAnimationFrame(tick); }
            }, { passive: true });
        })();
    </script>
</body>
</html>

