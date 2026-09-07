@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold tracking-tight text-[var(--text-primary)]">Registrasi Peserta</h2>
    <p class="text-sm text-[var(--text-secondary)] mt-1">Lengkapi data kepegawaian Anda untuk mengakses modul pelatihan.</p>
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

<form method="POST" action="{{ route('register.post') }}" class="space-y-4">
    @csrf
    
    <div>
        <label for="nip" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">NIP Pegawai <span class="text-danger">*</span></label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
            </span>
            <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required
                class="w-full pl-10 pr-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="Masukkan 18 digit NIP">
        </div>
    </div>

    <div>
        <label for="nama" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Nama Lengkap <span class="text-danger">*</span></label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[var(--text-muted)]">
                <i data-lucide="user" class="w-4 h-4"></i>
            </span>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                class="w-full pl-10 pr-4 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="Nama lengkap beserta gelar">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Email <span class="text-[var(--text-muted)] text-[10px] lowercase">(opsional)</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="nama@ditjenpas.go.id">
        </div>
        <div>
            <label for="golongan" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Golongan <span class="text-[var(--text-muted)] text-[10px] lowercase">(opsional)</span></label>
            <input type="text" id="golongan" name="golongan" value="{{ old('golongan') }}"
                class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="Contoh: III/a">
        </div>
    </div>

    <div>
        <label for="jabatan_id" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Jabatan (Eselon V) <span class="text-danger">*</span></label>
        <select id="jabatan_id" name="jabatan_id" required
            class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
            <option value="">-- Pilih Jabatan --</option>
            @foreach($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                    {{ $jabatan->nama_jabatan }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Password <span class="text-danger">*</span></label>
            <input type="password" id="password" name="password" required minlength="8"
                class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="Min. 8 karakter">
        </div>
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-[var(--text-secondary)] mb-1.5">Ulangi Password <span class="text-danger">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                class="w-full px-3.5 py-2.5 bg-[var(--input)] border border-[var(--border)] rounded-xl text-[var(--text-primary)] text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-[var(--text-muted)]"
                placeholder="Konfirmasi password">
        </div>
    </div>

    <button type="submit" class="w-full btn btn-primary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/20 hover:shadow-primary/30 transition-all flex items-center justify-center gap-2 mt-4">
        <span>Daftar Sekarang</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
    </button>
</form>

<div class="mt-6 pt-6 border-t border-[var(--border)] text-center text-sm text-[var(--text-secondary)]">
    Sudah memiliki akun terdaftar? <a href="{{ route('login') }}" class="text-primary hover:underline font-semibold">Masuk di sini</a>
</div>
@endsection
