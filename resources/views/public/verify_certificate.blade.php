<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat - SPEKTRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-[var(--card)] border border-[var(--border)] shadow-sm max-w-lg w-full rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-slate-900 px-6 py-8 text-center text-[var(--text-primary)]">
            <h1 class="text-2xl font-bold tracking-tight">Verifikasi Sertifikat</h1>
            <p class="text-[var(--text-secondary)] mt-2 text-sm">Sistem Pembelajaran Mandiri Pemasyarakatan</p>
        </div>
        
        <div class="p-6 md:p-8">
            @if($sertifikat)
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="badge-check" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Sertifikat Valid</h2>
                    <p class="text-[var(--text-muted)] text-sm mt-1">Sertifikat ini resmi diterbitkan oleh sistem kami.</p>
                </div>
                
                <div class="bg-slate-50 rounded-2xl p-5 space-y-4 border border-slate-100">
                    <div>
                        <p class="text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wider">Credential ID</p>
                        <p class="font-mono text-slate-800 font-medium">{{ $sertifikat->credential_id }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wider">Diberikan Kepada</p>
                        <p class="text-lg font-bold text-slate-800">{{ $sertifikat->user->nama }}</p>
                        <p class="text-sm text-[var(--text-muted)]">{{ $sertifikat->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wider">Pelatihan</p>
                        <p class="font-bold text-slate-800">{{ $sertifikat->pelatihan->judul }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[var(--text-secondary)] uppercase tracking-wider">Tanggal Terbit</p>
                        <p class="font-medium text-slate-800">{{ $sertifikat->issued_at->translatedFormat('d F Y H:i') }}</p>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center text-center py-8">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="x-circle" class="w-8 h-8 text-red-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Sertifikat Tidak Ditemukan</h2>
                    <p class="text-[var(--text-muted)] text-sm mt-2">Credential ID <strong>{{ $credential_id }}</strong> tidak terdaftar di sistem kami.</p>
                </div>
            @endif
            
            <div class="mt-8 text-center border-t border-slate-100 pt-6">
                <a href="/" class="text-sm font-semibold text-[#f0b429] hover:text-[#fcd34d]">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
    
    <script>lucide.createIcons();</script>
</body>
</html>


