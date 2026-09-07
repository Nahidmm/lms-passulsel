@extends('layouts.auth')

@section('title', 'Ubah Password Wajib')

@section('content')
<div class="text-center mb-6">
    <div class="w-14 h-14 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-500/20">
        <i data-lucide="shield-alert" class="w-7 h-7"></i>
    </div>
    <h2 class="text-2xl font-bold tracking-tight text-[var(--text-primary)] mb-1.5">Ganti Password Bawaan</h2>
    <p class="text-sm text-[var(--text-secondary)]">Demi keamanan data Anda, silakan perbarui password sebelum mengakses dashboard.</p>
</div>

@if($errors->any())
    <div class="p-4 rounded-xl bg-danger/10 border border-danger/20 text-danger text-sm mb-6 flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <ul class="list-disc pl-4 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('auth.change-password.post') }}" class="space-y-4">
    @csrf
    
    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Password Baru</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                <i data-lucide="lock" class="w-4 h-4"></i>
            </span>
            <input type="password" id="password" name="password" required minlength="8" autofocus
                class="w-full pl-10 pr-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="Minimal 8 karakter">
        </div>
    </div>

    <div>
        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Konfirmasi Password Baru</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
            </span>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                class="w-full pl-10 pr-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="Ketik ulang password baru">
        </div>
    </div>

    <button type="submit" class="w-full btn btn-primary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/20 hover:shadow-primary/30 transition-all flex items-center justify-center gap-2 mt-4">
        <span>Simpan & Lanjutkan</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
    </button>
</form>

<div class="mt-6 text-center">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm text-[var(--text-secondary)] hover:text-danger font-medium transition-colors inline-flex items-center gap-1.5">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span>Keluar Sesi</span>
        </button>
    </form>
</div>
@endsection
