@extends('layouts.auth')

@section('title', 'Menunggu Persetujuan')

@section('content')
<div class="text-center py-4">
    <div class="w-16 h-16 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-amber-500/20">
        <i data-lucide="clock" class="w-8 h-8"></i>
    </div>
    
    <h2 class="text-2xl font-bold tracking-tight text-[var(--text-primary)] mb-2">Pendaftaran Berhasil Terkirim</h2>
    <p class="text-sm text-[var(--text-secondary)] leading-relaxed mb-6">
        Akun kepegawaian Anda sedang dalam proses verifikasi dan persetujuan oleh Administrator Wilayah. Silakan hubungi admin kepegawaian atau periksa status secara berkala.
    </p>

    <div class="bg-[var(--card)] p-4 rounded-2xl border border-[var(--border)] text-left mb-6 space-y-2.5 shadow-xs">
        <div class="flex items-center justify-between text-sm">
            <span class="text-[var(--text-secondary)]">NIP Pegawai</span>
            <span class="font-mono font-semibold text-[var(--text-primary)]">{{ Auth::user()->nip ?? session('nip') }}</span>
        </div>
        <div class="flex items-center justify-between text-sm pt-2.5 border-t border-[var(--border)]">
            <span class="text-[var(--text-secondary)]">Status Akun</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                Menunggu Persetujuan
            </span>
        </div>
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full btn btn-secondary text-[var(--text-primary)] py-2.5 rounded-xl font-medium transition-all flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Halaman Masuk</span>
        </button>
    </form>
</div>
@endsection
