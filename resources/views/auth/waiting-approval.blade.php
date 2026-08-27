@extends('layouts.auth')

@section('title', 'Menunggu Persetujuan')

@section('content')

<div class="text-center py-6">
    <div class="w-20 h-20 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    
    <h2 class="text-xl font-bold text-text-primary mb-2">Pendaftaran Berhasil!</h2>
    <p class="text-text-secondary mb-6">
        Akun Anda sedang dalam status <strong>Menunggu Persetujuan</strong> dari Administrator. 
        Silakan hubungi Admin atau cek kembali secara berkala.
    </p>

    <div class="bg-secondary p-4 rounded-lg border border-border text-left mb-6">
        <div class="text-sm">
            <span class="text-text-secondary">NIP:</span>
            <span class="font-medium text-text-primary float-right">{{ Auth::user()->nip ?? session('nip') }}</span>
        </div>
        <div class="text-sm mt-2">
            <span class="text-text-secondary">Status:</span>
            <span class="font-bold text-warning float-right">Pending</span>
        </div>
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-bold py-2.5 px-4 rounded-lg transition-colors">
            Kembali ke Halaman Utama
        </button>
    </form>
</div>

@endsection

