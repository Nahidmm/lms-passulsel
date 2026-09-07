<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - STRAPSUSPAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo/strapsuspas.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #0b1120;
            color: #f1f5f9;
        }
        .auth-card {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-slate-950">

    {{-- Subtle decorative ambient lights --}}
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-sky-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-900/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md auth-card rounded-2xl relative z-10 overflow-hidden">
        <div class="p-8 sm:p-9">
            {{-- Brand Header --}}
            <div class="text-center mb-7">
                <div class="inline-flex items-center justify-center mb-3">
                    <img src="{{ asset('logo/strapsuspas.png') }}" alt="Logo STRAPSUSPAS" class="w-20 h-auto drop-shadow-lg object-contain">
                </div>
                <h1 class="text-2xl font-black text-white tracking-wider">STRAPSUSPAS</h1>
                <p class="text-[11px] text-sky-400 mt-1 font-semibold tracking-wide uppercase">Petugas Paten, Pembinaan Pasti, Pemasyarakatan Berdampak</p>
                <div class="inline-block mt-2 px-3 py-0.5 rounded-full bg-slate-800 border border-slate-700/60 text-[10px] text-slate-300 font-medium">
                    Kanwil Ditjenpas Sulsel
                </div>
            </div>

            @yield('content')
            
        </div>
        <div class="bg-slate-900/90 py-3.5 px-6 text-center text-[11px] text-slate-500 border-t border-slate-800/80">
            &copy; {{ date('Y') }} Kantor Wilayah Ditjen Pemasyarakatan Sulawesi Selatan
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
