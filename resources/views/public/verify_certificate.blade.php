<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat - STRAPSUSPAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo/strapsuspas.png') }}">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white max-w-lg w-full rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="bg-slate-900 px-6 py-7 text-center text-white">
            <div class="mb-3">
                <img src="{{ asset('logo/strapsuspas.png') }}" alt="Logo STRAPSUSPAS" class="w-14 h-14 object-contain mx-auto drop-shadow-md">
            </div>
            <h1 class="text-xl font-extrabold tracking-tight">Verifikasi Sertifikat Digital</h1>
            <p class="text-slate-400 mt-1 text-xs">STRAPSUSPAS &bull; Kantor Wilayah Ditjen Pemasyarakatan Sulawesi Selatan</p>
        </div>
        
        <div class="p-6 md:p-8">
            @if($sertifikat)
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-3">
                        <i data-lucide="badge-check" class="w-7 h-7"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Sertifikat Sah & Terverifikasi</h2>
                    <p class="text-slate-500 text-xs mt-0.5">Dokumen ini resmi diterbitkan oleh Kantor Wilayah Ditjenpas Sulsel.</p>
                </div>
                
                <div class="bg-slate-50 rounded-xl p-5 space-y-3.5 border border-slate-200/80 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ID Kredensial Registrasi</span>
                        <span class="font-mono text-slate-900 font-bold text-sm">{{ $sertifikat->credential_id }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Diberikan Kepada</span>
                        <span class="text-base font-bold text-slate-900 block mt-0.5">{{ $sertifikat->user->nama }}</span>
                        <span class="text-[11px] text-slate-500">NIP: {{ $sertifikat->user->nip ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Pelatihan</span>
                        <span class="font-semibold text-slate-800 block mt-0.5">{{ $sertifikat->pelatihan->judul }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Penerbitan</span>
                        <span class="font-medium text-slate-700 block mt-0.5">{{ $sertifikat->issued_at->translatedFormat('l, d F Y H:i') }} WITA</span>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center text-center py-6">
                    <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-3">
                        <i data-lucide="x-circle" class="w-7 h-7"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Sertifikat Tidak Ditemukan</h2>
                    <p class="text-slate-500 text-xs mt-1">ID Kredensial <strong>{{ $credential_id }}</strong> tidak terdaftar dalam database kami.</p>
                </div>
            @endif
            
            <div class="mt-6 text-center border-t border-slate-100 pt-5">
                <a href="/" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    
    <script>lucide.createIcons();</script>
</body>
</html>
