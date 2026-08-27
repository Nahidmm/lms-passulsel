@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('content')

@if($errors->any())
    <div class="bg-danger/10 border border-danger/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register.post') }}" class="space-y-4">
    @csrf
    
    <div>
        <label for="nip" class="block text-sm font-medium text-text-primary mb-1">NIP Pegawai <span class="text-danger">*</span></label>
        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required
            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
            placeholder="Masukkan NIP (tanpa spasi)">
    </div>

    <div>
        <label for="nama" class="block text-sm font-medium text-text-primary mb-1">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
            placeholder="Masukkan nama lengkap beserta gelar">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="email" class="block text-sm font-medium text-text-primary mb-1">Email <span class="text-text-secondary text-xs">(opsional)</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
                placeholder="email@contoh.com">
        </div>
        <div>
            <label for="golongan" class="block text-sm font-medium text-text-primary mb-1">Golongan <span class="text-text-secondary text-xs">(opsional)</span></label>
            <input type="text" id="golongan" name="golongan" value="{{ old('golongan') }}"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
                placeholder="Contoh: III/b">
        </div>
    </div>

    <div>
        <label for="jabatan_id" class="block text-sm font-medium text-text-primary mb-1">Jabatan (Eselon V) <span class="text-danger">*</span></label>
        <select id="jabatan_id" name="jabatan_id" required
            class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow bg-[var(--card)] border border-[var(--border)] shadow-sm">
            <option value="">-- Pilih Jabatan --</option>
            @foreach($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                    {{ $jabatan->nama_jabatan }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="password" class="block text-sm font-medium text-text-primary mb-1">Password <span class="text-danger">*</span></label>
            <input type="password" id="password" name="password" required minlength="8"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
                placeholder="Min. 8 karakter">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-1">Ulangi Password <span class="text-danger">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                class="w-full px-4 py-2 border border-border rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-shadow"
                placeholder="Ulangi password">
        </div>
    </div>

    <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-[var(--text-primary)] font-bold py-2.5 px-4 rounded-lg transition-colors mt-2">
        Daftar
    </button>
</form>

<div class="mt-6 pt-6 border-t border-border text-center text-sm text-text-secondary">
    Sudah memiliki akun? <a href="{{ route('login') }}" class="text-accent hover:text-accent-hover font-semibold">Login di sini</a>
</div>

@endsection

