@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Selamat datang, {{ explode(' ', Auth::user()->nama)[0] }}!</h1>
    <p class="text-text-secondary mt-1">Pantau progres belajar dan asah kemampuan Anda di LMS Pas Sulsel.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8">
    
    <!-- Stat: Modul Dibaca -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-6 flex items-start gap-4 hover:border-accent transition-colors">
        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary shrink-0">
            <i data-lucide="book-open-check" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-text-secondary mb-1">Modul Dipelajari</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-3xl font-display font-bold text-text-primary">{{ $modulDibaca }}</h3>
                <span class="text-sm font-medium text-text-secondary">/ {{ $totalModul }}</span>
            </div>
        </div>
    </div>

    <!-- Stat: Nilai Evaluasi -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-6 flex items-start gap-4 hover:border-accent transition-colors">
        <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center text-accent shrink-0">
            <i data-lucide="award" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-text-secondary mb-1">Rata-rata Skor Evaluasi</p>
            <h3 class="text-3xl font-display font-bold text-text-primary">{{ $rataNilai }}</h3>
        </div>
    </div>

    <!-- Stat: Progres Keseluruhan -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-6 flex flex-col justify-between hover:border-accent transition-colors">
        <div>
            <div class="flex justify-between items-center mb-1">
                <p class="text-sm font-medium text-text-secondary">Progres Belajar</p>
                <span class="text-sm font-bold text-primary">{{ $progres }}%</span>
            </div>
            <h3 class="text-2xl font-display font-bold text-text-primary hidden">Progres</h3>
        </div>
        <div class="mt-4">
            <div class="w-full bg-secondary rounded-full h-2.5 overflow-hidden">
                <div class="bg-primary h-2.5 rounded-full" style="width: {{ $progres }}%"></div>
            </div>
        </div>
    </div>

</div>

<!-- AI Assistant Promo Panel -->
@if(!Auth::user()->hasActiveSesiEvaluasi())
<div class="bg-gradient-to-br from-primary to-primary-hover rounded-2xl shadow-lg border border-accent/30 overflow-hidden mb-8 relative">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

    <div class="p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 relative z-10">
        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-inner shrink-0 rotate-3">
            <i data-lucide="bot" class="w-10 h-10 text-primary"></i>
        </div>
        
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-xl md:text-2xl font-display font-bold text-white mb-2">AI Assistant Cerdas</h2>
            <p class="text-white/80 text-sm md:text-base max-w-2xl">
                Tanyakan apa saja tentang tugas pokok, fungsi, atau regulasi pemasyarakatan. AI kami siap membantu Anda mencari panduan dan contoh laporan.
            </p>
        </div>

        <div class="shrink-0 w-full md:w-auto">
            <a href="{{ route('ai.index') }}" class="block w-full md:w-auto text-center bg-accent hover:bg-accent-hover text-primary font-bold py-3 px-6 rounded-lg shadow-md transition-colors">
                Mulai Bertanya
            </a>
        </div>
    </div>
</div>
@endif

<!-- Quick Actions & Info -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Lanjutkan Pembelajaran -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-6">
        <h3 class="text-lg font-display font-bold text-text-primary mb-4">Lanjutkan Belajar</h3>
        <p class="text-sm text-text-secondary mb-6">Akses modul pembelajaran sesuai dengan urutan yang telah ditetapkan.</p>
        <a href="{{ route('peserta.pembelajaran.index') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Buka Modul
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    <!-- Evaluasi -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-6">
        <h3 class="text-lg font-display font-bold text-text-primary mb-4">Evaluasi Kompetensi</h3>
        <p class="text-sm text-text-secondary mb-6">Uji pemahaman Anda melalui kuis pilihan ganda yang disesuaikan dengan jabatan.</p>
        <a href="{{ route('peserta.evaluasi.index') }}" class="inline-flex items-center gap-2 border border-primary text-primary hover:bg-primary/5 font-medium py-2 px-4 rounded-lg transition-colors">
            Lihat Evaluasi
            <i data-lucide="file-text" class="w-4 h-4"></i>
        </a>
    </div>
</div>

@endsection
