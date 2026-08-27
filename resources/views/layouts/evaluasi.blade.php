<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Berlangsung - SPEKTRA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="game-shell font-sans antialiased min-h-screen flex flex-col overflow-x-hidden text-[var(--text-primary)] select-none">

    <!-- Header Only (No Sidebar) -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-[#0d1117]/90 border-b border-[var(--border)] h-14 flex items-center justify-between px-4 lg:px-8">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-rose-600 to-orange-500 flex items-center justify-center">
                <i data-lucide="skull" class="w-4 h-4 text-[var(--text-primary)]"></i>
            </div>
            <div>
                <div class="font-display font-black text-[var(--text-primary)] text-sm leading-none">Boss Battle</div>
                <div class="text-[9px] text-rose-400 font-black uppercase tracking-widest">Live</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div id="countdown-timer" class="flex items-center gap-2 bg-rose-500/15 text-rose-400 border border-rose-500/30 px-4 py-1.5 rounded-full font-black text-sm" style="box-shadow:0 0 15px rgba(239,68,68,0.2)">
                <i data-lucide="timer" class="w-4 h-4"></i>
                <span>--:--</span>
            </div>
            <div class="h-6 w-px bg-[var(--card)] border border-[var(--border)] shadow-sm"></div>
            <div class="text-sm font-bold text-[var(--text-secondary)]">{{ Auth::user()->nama }}</div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-4 md:p-6 lg:p-8 flex-1 max-w-7xl mx-auto w-full">
        @if(session('error'))
            <div class="alert-error flex items-start gap-3 mb-6">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-red-400"></i>
                <div class="font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
    <script>
        lucide.createIcons();
        
        // Prevent right click & copy
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('copy', event => {
            event.clipboardData.setData('text/plain', 'Menyontek tidak diperbolehkan.');
            event.preventDefault();
        });
    </script>
</body>
</html>

