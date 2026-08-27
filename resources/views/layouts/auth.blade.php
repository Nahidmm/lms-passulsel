<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - SPEKTRA</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">
    
    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Almarai', sans-serif;
            background-color: #000;
            color: #E1E0CC;
        }
        .font-instrument { font-family: 'Instrument Serif', serif; }
        .text-prisma { color: #DEDBC8; }
        .bg-prisma { background-color: #DEDBC8; }
        
        .noise-overlay {
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.7;
            mix-blend-mode: overlay;
            pointer-events: none;
            z-index: 10;
        }

        /* Override form styles for cinematic theme */
        input[type="text"], input[type="password"], input[type="email"], select {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #E1E0CC !important;
            border-radius: 0.75rem !important;
        }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus {
            border-color: #DEDBC8 !important;
            box-shadow: 0 0 0 2px rgba(222, 219, 200, 0.2) !important;
        }
        label {
            color: rgba(225, 224, 204, 0.8) !important;
        }
        button[type="submit"] {
            background-color: #DEDBC8 !important;
            color: #000 !important;
            border-radius: 9999px !important; /* full */
            transition: all 0.3s ease !important;
        }
        button[type="submit"]:hover {
            transform: scale(1.02);
            background-color: #fff !important;
        }
        a {
            color: #DEDBC8 !important;
        }
        a:hover {
            color: #fff !important;
        }
    </style>
</head>
<body class="antialiased min-h-screen relative flex items-center justify-center p-4">

    <!-- Background Video -->
    <video 
        src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260405_170732_8a9ccda6-5cff-4628-b164-059c500a2b41.mp4" 
        autoplay loop muted playsinline 
        class="fixed inset-0 w-full h-full object-cover z-0 opacity-40"
    ></video>
    
    <div class="noise-overlay"></div>
    <div class="fixed inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/80 pointer-events-none z-0"></div>

    <div class="w-full max-w-md bg-black/40 backdrop-blur-xl border border-white/10 rounded-[2rem] shadow-2xl relative z-20 overflow-hidden">
        <div class="p-8 sm:p-10">
            <div class="flex justify-center mb-6">
                <!-- App Logo -->
                <img src="{{ asset('logo/logo.png') }}" alt="SPEKTRA Logo" class="w-24 h-auto object-contain drop-shadow-lg">
            </div>
            
            <h1 class="text-3xl font-instrument italic text-center text-prisma mb-1">SPEKTRA</h1>
            <p class="text-white/60 text-center mb-8 text-sm">Platform Edukasi & Evaluasi Terpadu</p>

            @yield('content')
            
        </div>
        <div class="bg-black/50 p-4 text-center text-[10px] sm:text-xs text-white/40 border-t border-white/5">
            &copy; {{ date('Y') }} Kantor Wilayah Kemenimipas (Kanwil Ditjenpas Sulsel)
        </div>
    </div>

</body>

