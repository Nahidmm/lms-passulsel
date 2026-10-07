<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server | STRAPSUSPAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo/strapsuspas.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #0b1120;
            color: #f1f5f9;
        }
        .error-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-slate-950">

    <!-- Decorative ambient lights -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-rose-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-lg error-card rounded-2xl relative z-10 overflow-hidden text-center p-8 sm:p-10">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 mb-6">
            <i data-lucide="server-off" class="w-10 h-10"></i>
        </div>

        <h1 class="text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-amber-400 mb-2">500</h1>
        <h2 class="text-xl font-bold text-white mb-3">Terjadi Kesalahan Server</h2>
        <p class="text-sm text-slate-400 max-w-md mx-auto mb-8 leading-relaxed">
            Terjadi kendala internal pada sistem saat memproses permintaan Anda. Silakan coba beberapa saat lagi.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold rounded-full text-sm transition-all shadow-lg shadow-rose-500/20">
                <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                Coba Lagi
            </button>
            <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-full text-sm border border-slate-700 transition-all">
                <i data-lucide="home" class="w-4 h-4"></i>
                Ke Beranda
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800/80 text-xs text-slate-500">
            STRAPSUSPAS &bull; Kanwil Ditjen Pemasyarakatan Sulawesi Selatan
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
