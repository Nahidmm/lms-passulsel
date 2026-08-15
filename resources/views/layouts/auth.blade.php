<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - LMS Pas Sulsel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="bg-secondary text-text-primary font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-border overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex justify-center mb-6">
                <!-- Placeholder Logo -->
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white font-display font-bold text-xl shadow-md">
                    PAS
                </div>
            </div>
            
            <h1 class="text-2xl font-display font-bold text-center text-primary mb-2">LMS Pas Sulsel</h1>
            <p class="text-text-secondary text-center mb-8 text-sm">Pengembangan Kompetensi Eselon V</p>

            @yield('content')
            
        </div>
        <div class="bg-secondary p-4 text-center text-xs text-text-secondary border-t border-border">
            &copy; {{ date('Y') }} Kantor Wilayah Kemenkumham Sulawesi Selatan
        </div>
    </div>

</body>
</html>
