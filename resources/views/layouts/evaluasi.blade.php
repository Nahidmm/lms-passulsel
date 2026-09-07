<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Evaluasi Pembelajaran') - STRAPSUSPAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
<body class="antialiased min-h-screen flex flex-col bg-[var(--bg)] text-[var(--text-primary)] select-none">

    <!-- Header (No Sidebar for focus mode) -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-[var(--surface)]/90 border-b border-[var(--border)] h-16 flex items-center justify-between px-4 lg:px-8 shadow-xs">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo/strapsuspas.png') }}" alt="STRAPSUSPAS" class="w-9 h-9 object-contain">
            <div>
                <div class="font-bold text-[var(--text-primary)] text-sm leading-tight">Evaluasi Pembelajaran &bull; STRAPSUSPAS</div>
                <div class="text-[11px] text-[var(--text-secondary)]">Kanwil Ditjenpas Sulsel</div>
            </div>
        </div>

        <div class="flex items-center gap-3 md:gap-4">
            <div id="header-timer-box" class="flex items-center gap-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 px-3.5 py-1.5 rounded-full font-bold text-xs shadow-xs">
                <i data-lucide="timer" class="w-4 h-4"></i>
                <span id="header-timer-val" class="font-mono text-sm">--:--</span>
            </div>
            <div class="h-5 w-px bg-[var(--border)] hidden sm:block"></div>
            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-[var(--text-secondary)]">
                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                <span>{{ Auth::user()->nama }}</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-4 md:p-6 lg:p-8 flex-1 max-w-7xl mx-auto w-full">
        @if(session('error'))
            <div class="alert alert-error mb-6">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <div class="font-semibold text-sm">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
    <script>
        lucide.createIcons();
        
        // Prevent accidental right click & copy during exam
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('copy', event => {
            event.clipboardData.setData('text/plain', 'Menyalin soal tidak diperkenankan.');
            event.preventDefault();
        });
    </script>
</body>
</html>
