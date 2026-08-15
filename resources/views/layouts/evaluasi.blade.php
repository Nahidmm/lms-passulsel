<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Berlangsung - LMS Pas Sulsel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="bg-secondary text-text-primary font-sans antialiased min-h-screen flex flex-col overflow-x-hidden select-none">

    <!-- Header Only (No Sidebar) -->
    <header class="bg-white border-b border-border h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo/logo.png') }}" alt="Logo LMS" class="h-8 w-auto object-contain">
            <div class="font-display font-bold text-primary text-lg hidden sm:block">LMS Pas Sulsel</div>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-warning/10 text-warning px-4 py-1.5 rounded-full font-bold flex items-center gap-2 border border-warning/20">
                <i data-lucide="timer" class="w-4 h-4"></i>
                <span id="countdown-timer">--:--</span>
            </div>
            <div class="h-8 w-px bg-border"></div>
            <div class="text-sm font-medium text-text-secondary">{{ Auth::user()->nama }}</div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-4 md:p-6 lg:p-8 flex-1 max-w-7xl mx-auto w-full">
        @if(session('error'))
            <div class="bg-danger text-white px-4 py-3 rounded-lg mb-6 shadow-sm flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <div>{{ session('error') }}</div>
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
