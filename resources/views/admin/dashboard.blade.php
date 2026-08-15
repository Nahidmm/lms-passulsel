@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-display font-bold text-primary">Ringkasan Sistem</h1>
    <p class="text-text-secondary mt-1">Pantau aktivitas pengguna dan statistik LMS Pas Sulsel.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    
    <!-- Stat 1 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-primary transition-colors">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Total Peserta Aktif</p>
        </div>
        <h3 class="text-3xl font-display font-bold text-text-primary ml-13">{{ $totalPengguna }}</h3>
    </div>

    <!-- Stat 2 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-warning transition-colors">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-warning/10 rounded-lg flex items-center justify-center text-warning">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Sesi Evaluasi Aktif</p>
        </div>
        <h3 class="text-3xl font-display font-bold text-text-primary ml-13">{{ $penggunaAktif }}</h3>
    </div>

    <!-- Stat 3 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-danger transition-colors relative">
        @if($pendingRequests > 0)
            <div class="absolute top-4 right-4 w-3 h-3 bg-danger rounded-full animate-pulse"></div>
        @endif
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-danger/10 rounded-lg flex items-center justify-center text-danger">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Pendaftaran Pending</p>
        </div>
        <h3 class="text-3xl font-display font-bold text-text-primary ml-13">{{ $pendingRequests }}</h3>
    </div>

    <!-- Stat 4 -->
    <div class="bg-white rounded-xl shadow-sm border border-border p-5 hover:border-success transition-colors">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center text-success">
                <i data-lucide="bar-chart" class="w-5 h-5"></i>
            </div>
            <p class="text-sm font-medium text-text-secondary">Skor Max / Min</p>
        </div>
        <div class="flex items-baseline gap-2 ml-13">
            <h3 class="text-3xl font-display font-bold text-success">{{ $highestScore }}</h3>
            <span class="text-xl font-bold text-text-secondary">/</span>
            <h3 class="text-3xl font-display font-bold text-danger">{{ $lowestScore }}</h3>
        </div>
    </div>

</div>

<!-- Quick Links Admin -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-border p-6">
        <h3 class="font-display font-bold text-lg mb-4 flex items-center gap-2">
            <i data-lucide="settings" class="w-5 h-5 text-primary"></i> Manajemen Cepat
        </h3>
        <div class="flex flex-col gap-3">
            <a href="{{ route('admin.akun.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-border hover:bg-secondary transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded flex items-center justify-center"><i data-lucide="users-check" class="w-4 h-4"></i></div>
                    <span class="font-medium text-text-primary">Kelola Persetujuan Akun</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-text-secondary"></i>
            </a>
            <a href="{{ route('admin.statistik.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-border hover:bg-secondary transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded flex items-center justify-center"><i data-lucide="clipboard-data" class="w-4 h-4"></i></div>
                    <span class="font-medium text-text-primary">Lihat Rekap Nilai Peserta</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-text-secondary"></i>
            </a>
            <a href="{{ route('admin.materi.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-border hover:bg-secondary transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded flex items-center justify-center"><i data-lucide="book-plus" class="w-4 h-4"></i></div>
                    <span class="font-medium text-text-primary">Tambah Modul Baru</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-text-secondary"></i>
            </a>
        </div>
    </div>
</div>

@endsection
