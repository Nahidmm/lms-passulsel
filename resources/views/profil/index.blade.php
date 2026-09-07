@extends('layouts.app')
@section('title', 'Profil Pengguna - STRAPSUSPAS')

@section('content')
<div class="space-y-6 py-2 max-w-5xl mx-auto">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-extrabold text-[var(--text-primary)]">Pengaturan Profil & Keamanan</h1>
        <p class="text-xs text-[var(--text-secondary)] mt-1">Kelola data identitas pegawai, kontak, dan kata sandi akun LMS Anda.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Profile Info Card --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 text-center shadow-xs sticky top-24">
                <div class="relative w-28 h-28 mx-auto mb-4">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->nama }}" class="w-full h-full object-cover rounded-2xl ring-4 ring-indigo-500/10 shadow-sm">
                </div>
                <h2 class="text-lg font-bold text-[var(--text-primary)]">{{ $user->nama }}</h2>
                <p class="text-xs text-[var(--text-secondary)] font-mono mt-0.5">{{ $user->nip ?? '-' }}</p>
                
                <div class="mt-4 pt-4 border-t border-[var(--border)] space-y-2 text-left text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[var(--text-secondary)]">Peran:</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-800">
                            {{ $user->role_label ?? ucfirst($user->role) }}
                        </span>
                    </div>
                    @if($user->jabatan)
                    <div class="flex items-center justify-between">
                        <span class="text-[var(--text-secondary)]">Jabatan:</span>
                        <span class="font-semibold text-[var(--text-primary)] truncate max-w-[150px]">{{ $user->jabatan->nama_jabatan }}</span>
                    </div>
                    @endif
                    @if($user->unitKerja)
                    <div class="flex items-center justify-between">
                        <span class="text-[var(--text-secondary)]">Unit Kerja:</span>
                        <span class="font-semibold text-[var(--text-primary)] truncate max-w-[150px]">{{ $user->unitKerja->nama }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Forms Column --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Update Profile Form --}}
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 md:p-8 shadow-xs">
                <h3 class="text-base font-bold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-indigo-600"></i> Informasi Pribadi
                </h3>

                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="nama" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Nama Lengkap & Gelar</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                        @error('nama') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                        @error('email') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="avatar" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Ganti Foto Profil</label>
                        <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                        <p class="text-[11px] text-[var(--text-muted)] mt-1">Format: JPG, PNG. Ukuran maksimal 2MB.</p>
                        @error('avatar') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn btn-primary text-xs px-5 py-2.5">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Update Password Form --}}
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 md:p-8 shadow-xs">
                <h3 class="text-base font-bold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                    <i data-lucide="shield" class="w-4 h-4 text-amber-500"></i> Ganti Kata Sandi
                </h3>

                <form action="{{ route('profil.password') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                        @error('current_password') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Kata Sandi Baru</label>
                            <input type="password" id="password" name="password" required minlength="8"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                            @error('password') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-[var(--text-secondary)] mb-1">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[var(--border)] bg-[var(--surface)] text-[var(--text-primary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn btn-secondary text-xs px-5 py-2.5">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
